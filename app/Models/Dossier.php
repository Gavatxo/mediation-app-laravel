<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasOne, HasMany, MorphMany};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\TracksAccess;

class Dossier extends Model
{
    use HasFactory, TracksAccess;

    protected $fillable = [
        'type', 'reference', 'titre', 'descriptif', 'statut',
        'mediateur_id', 'comediateur_id', 'centre_id', 'assistante_id',
        'acces_id', 'acces_date', 'saisine', 'cloture'
    ];

    protected $casts = [
        'saisine' => 'datetime',
        'cloture' => 'datetime',
        'acces_date' => 'datetime'
    ];

    // Pour Inertia : attributs automatiquement inclus dans JSON
    protected $appends = [
        'is_actif',
        'duree_traitement',
        'statut_label',
        'type_label'
    ];

    // ===============================
    // RELATIONS VERS TIERS UNIFIÉ
    // ===============================

    public function mediateur(): BelongsTo
    {
        return $this->belongsTo(Tiers::class, 'mediateur_id');
    }

    public function comediateur(): BelongsTo
    {
        return $this->belongsTo(Tiers::class, 'comediateur_id');
    }

    public function centre(): BelongsTo
    {
        return $this->belongsTo(Tiers::class, 'centre_id');
    }

    public function assistante(): BelongsTo
    {
        return $this->belongsTo(Tiers::class, 'assistante_id');
    }

    // ===============================
    // RELATIONS FUTURES (préparées)
    // ===============================

    public function dossierJudiciaire(): HasOne
    {
        return $this->hasOne(DossierJudiciaire::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class, 'parent_id')
                    ->where('parent_type', 'dossier');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'parent');
    }

    // Relations polymorphes pour les parties
    public function parties(): MorphMany
    {
        return $this->morphMany(Relation::class, 'pere')
                    ->where('type', 1); // 1 = partie dans dossier
    }

    // ===============================
    // SCOPES MÉTIER
    // ===============================

    public function scopeActifs(Builder $query): Builder
    {
        return $query->whereNull('cloture')
                    ->where('statut', '!=', 99); // 99 = archivé
    }

    public function scopeClos(Builder $query): Builder
    {
        return $query->whereNotNull('cloture')
                    ->orWhere('statut', 99);
    }

    public function scopeJudiciaires(Builder $query): Builder
    {
        return $query->whereHas('dossierJudiciaire');
    }

    public function scopeParMediateur(Builder $query, int $mediateurId): Builder
    {
        return $query->where('mediateur_id', $mediateurId);
    }

    public function scopeParType(Builder $query, int $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeParStatut(Builder $query, int $statut): Builder
    {
        return $query->where('statut', $statut);
    }

    // ===============================
    // ACCESSORS POUR REACT/INERTIA
    // ===============================

    public function getIsActifAttribute(): bool
    {
        return $this->cloture === null && $this->statut !== 99;
    }

    public function getDureeTraitementAttribute(): ?string
    {
        if (!$this->saisine) return null;
        
        $fin = $this->cloture ?? now();
        return $this->saisine->diffForHumans($fin, true);
    }

    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            1 => 'Ouvert',
            2 => 'En cours',
            3 => 'Suspendu',
            4 => 'En attente',
            9 => 'Clos',
            99 => 'Archivé',
            default => 'Inconnu'
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            101 => 'Médiation Civile',
            102 => 'Médiation Commerciale',
            103 => 'Médiation Administrative',
            104 => 'Médiation Familiale',
            105 => 'Médiation Pénale',
            default => 'Type ' . $this->type
        };
    }

    public function getIsJudiciaireAttribute(): bool
    {
        return $this->dossierJudiciaire !== null;
    }

    // ===============================
    // MÉTHODES UTILITAIRES
    // ===============================

    /**
     * Clôturer le dossier
     */
    public function cloturer(?string $motif = null): void
    {
        $this->update([
            'cloture' => now(),
            'statut' => 9
        ]);
    }

    /**
     * Réouvrir le dossier
     */
    public function reouvrir(): void
    {
        $this->update([
            'cloture' => null,
            'statut' => 2
        ]);
    }

    /**
     * Méthode optimisée pour Inertia
     */
    public static function forInertia()
    {
        return static::with([
                'mediateur:id,nom,prenom,denomination',
                'comediateur:id,nom,prenom,denomination',
                'centre:id,denomination',
                'assistante:id,nom,prenom'
            ])
            ->select([
                'id', 'reference', 'titre', 'statut', 'type',
                'mediateur_id', 'comediateur_id', 'centre_id', 'assistante_id',
                'saisine', 'cloture', 'acces_date', 'created_at'
            ]);
    }

    /**
     * Données formatées pour les selects React
     */
    public function toSelectOption(): array
    {
        return [
            'value' => $this->id,
            'label' => $this->reference . ' - ' . $this->titre,
            'type' => $this->type_label,
            'statut' => $this->statut_label,
            'isActif' => $this->is_actif,
        ];
    }

    /**
     * Résumé pour dashboard
     */
    public function toSummary(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'titre' => $this->titre,
            'mediateur' => $this->mediateur->full_name,
            'statut' => $this->statut_label,
            'type' => $this->type_label,
            'saisine' => $this->saisine?->format('d/m/Y'),
            'duree' => $this->duree_traitement,
            'is_actif' => $this->is_actif,
            'last_access' => $this->last_access,
        ];
    }
}