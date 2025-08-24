<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait TracksAccess
{
    /**
     * Mettre à jour l'accès pour l'utilisateur actuel ou spécifié
     */
    public function updateAccess(?int $userId = null): void
    {
        $this->update([
            'acces_id' => $userId ?? auth()->id(),
            'acces_date' => now()
        ]);
    }

    /**
     * Scope pour les éléments récemment accédés
     */
    public function scopeRecentlyAccessed(Builder $query, int $hours = 24): Builder
    {
        return $query->where('acces_date', '>=', now()->subHours($hours))
                    ->whereNotNull('acces_date');
    }

    /**
     * Scope pour les éléments accédés par un utilisateur spécifique
     */
    public function scopeAccessedBy(Builder $query, int $userId): Builder
    {
        return $query->where('acces_id', $userId);
    }

    /**
     * Scope pour trier par accès récent
     */
    public function scopeOrderByRecentAccess(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderBy('acces_date', $direction);
    }

    /**
     * Accessor pour obtenir le dernier accès de façon lisible
     */
    public function getLastAccessAttribute(): ?string
    {
        return $this->acces_date?->diffForHumans();
    }

    /**
     * Accessor pour vérifier si l'élément a été accédé récemment
     */
    public function getIsRecentlyAccessedAttribute(): bool
    {
        if (!$this->acces_date) {
            return false;
        }
        
        return $this->acces_date->isAfter(now()->subHours(24));
    }
}