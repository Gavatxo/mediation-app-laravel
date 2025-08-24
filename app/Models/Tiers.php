<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{HasMany, BelongsTo, MorphMany};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\TracksAccess;

class Tiers extends Model
{
    use HasFactory, TracksAccess;

    protected $fillable = [
        'reference', 'nom', 'prenom', 'denomination',
        'avatar', 'identifiant', 'mot_passe', 'siret',
        'acces_id', 'acces_date', 'parent_id'
    ];

    protected $casts = [
        'acces_date' => 'datetime'
    ];

    // Pour Inertia : attributs automatiquement inclus dans JSON
    protected $appends = [
        'full_name', 
        'type_entity', 
        'is_personne', 
        'is_juridiction',
        'is_tribunal'
    ];

    // ===============================
    // RELATIONS HIÉRARCHIQUES
    // ===============================

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Tiers::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Tiers::class, 'parent_id');
    }

    // ===============================
    // RELATIONS MÉTIER (futures)
    // ===============================

    public function dossiersMediateur(): HasMany
    {
        return $this->hasMany(Dossier::class, 'mediateur_id');
    }

    public function dossiersComediateur(): HasMany
    {
        return $this->hasMany(Dossier::class, 'comediateur_id');
    }

    public function dossiersAssistante(): HasMany
    {
        return $this->hasMany(Dossier::class, 'assistante_id');
    }

    // Relations polymorphes (futures)
    public function relations(): MorphMany
    {
        return $this->morphMany(Relation::class, 'pere');
    }

    // ===============================
    // SCOPES POUR DIFFÉRENCIER LES TYPES
    // ===============================

    public function scopePersonnes(Builder $query): Builder
    {
        return $query->whereNull('parent_id')
                    ->whereNotNull('nom')
                    ->whereNull('denomination');
    }

    public function scopeJuridictions(Builder $query): Builder
    {
        return $query->where(function($q) {
            // Juridictions avec parent (chambres, sections)
            $q->whereNotNull('parent_id')
              // OU juridictions racines (tribunaux)
              ->orWhere(function($subQ) {
                  $subQ->whereNull('parent_id')
                       ->whereNull('nom')
                       ->whereNotNull('denomination');
              });
        });
    }

    public function scopeTribunaux(Builder $query): Builder
    {
        return $query->juridictions()->whereNull('parent_id');
    }

    public function scopeChambres(Builder $query): Builder
    {
        return $query->juridictions()->whereNotNull('parent_id');
    }

    public function scopePersonnesMorales(Builder $query): Builder
    {
        return $query->whereNull('parent_id')
                    ->whereNull('nom')
                    ->whereNotNull('denomination');
    }

    // ===============================
    // ACCESSORS POUR REACT/INERTIA
    // ===============================

    public function getFullNameAttribute(): string
    {
        if ($this->prenom && $this->nom) {
            return trim("{$this->prenom} {$this->nom}");
        }
        
        if ($this->nom) {
            return $this->nom;
        }
        
        return $this->denomination ?? 'Sans nom';
    }

    public function getIsPersonneAttribute(): bool
    {
        return $this->nom !== null && $this->parent_id === null;
    }

    public function getIsJuridictionAttribute(): bool
    {
        return $this->parent_id !== null || 
               ($this->nom === null && $this->denomination !== null);
    }

    public function getIsTribunalAttribute(): bool
    {
        return $this->is_juridiction && $this->parent_id === null;
    }

    public function getTypeEntityAttribute(): string
    {
        return match(true) {
            $this->is_personne => 'Personne',
            $this->is_tribunal => 'Tribunal',
            $this->is_juridiction => 'Chambre',
            default => 'Personne morale'
        };
    }

    // ===============================
    // MÉTHODES POUR INERTIA/REACT
    // ===============================

    /**
     * Récupère la hiérarchie complète pour l'affichage
     */
    public function getHierarchyPath(): string
    {
        $path = [];
        $current = $this;
        
        while ($current) {
            array_unshift($path, $current->full_name);
            $current = $current->parent;
        }
        
        return implode(' > ', $path);
    }

    /**
     * Méthode optimisée pour récupérer les données Inertia
     */
    public static function forInertia()
    {
        return static::with(['parent:id,nom,prenom,denomination', 'children:id,nom,prenom,denomination,parent_id'])
                    ->select([
                        'id', 'nom', 'prenom', 'denomination', 'parent_id', 
                        'acces_date', 'reference', 'avatar'
                    ]);
    }

    /**
     * Données formatées pour les selects React
     */
    public function toSelectOption(): array
    {
        return [
            'value' => $this->id,
            'label' => $this->full_name,
            'type' => $this->type_entity,
            'isJuridiction' => $this->is_juridiction,
        ];
    }
}