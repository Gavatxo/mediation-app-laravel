<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Dossier, Tiers};

class DossierFactory extends Factory
{
    protected $model = Dossier::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement([101, 102, 103, 104, 105]),
            'reference' => $this->faker->unique()->numerify('D####-###'),
            'titre' => $this->faker->sentence(4),
            'descriptif' => $this->faker->optional()->paragraph,
            'statut' => $this->faker->randomElement([1, 2, 3, 4]),
            'mediateur_id' => Tiers::factory()->create()->id,
            'saisine' => $this->faker->dateTimeBetween('-2 years', 'now'),
        ];
    }

    /**
     * Dossier actif (non clos)
     */
    public function actif(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => $this->faker->randomElement([1, 2, 3]),
            'cloture' => null,
        ]);
    }

    /**
     * Dossier clos
     */
    public function clos(): static
    {
        return $this->state(function (array $attributes) {
            $saisine = $attributes['saisine'] ?? $this->faker->dateTimeBetween('-2 years', '-1 month');
            
            return [
                'statut' => 9,
                'cloture' => $this->faker->dateTimeBetween($saisine, 'now'),
            ];
        });
    }

    /**
     * Dossier judiciaire
     */
    public function judiciaire(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $this->faker->randomElement([101, 102, 103]),
        ]);
    }

    /**
     * Dossier avec comédiateur
     */
    public function avecComediateur(): static
    {
        return $this->state(fn (array $attributes) => [
            'comediateur_id' => Tiers::factory()->create()->id,
        ]);
    }

    /**
     * Dossier avec assistante
     */
    public function avecAssistante(): static
    {
        return $this->state(fn (array $attributes) => [
            'assistante_id' => Tiers::factory()->create()->id,
        ]);
    }

    /**
     * Dossier avec centre
     */
    public function avecCentre(): static
    {
        return $this->state(fn (array $attributes) => [
            'centre_id' => Tiers::factory()->tribunal()->create()->id,
        ]);
    }

    /**
     * Dossier avec suivi d'accès
     */
    public function withAccess(int $userId = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'acces_id' => $userId,
            'acces_date' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Dossier récent
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'saisine' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'statut' => $this->faker->randomElement([1, 2]),
        ]);
    }

    /**
     * Dossier médiation civile
     */
    public function mediationCivile(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 101,
            'titre' => 'Médiation civile - ' . $this->faker->words(3, true),
        ]);
    }

    /**
     * Dossier médiation commerciale
     */
    public function mediationCommerciale(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 102,
            'titre' => 'Médiation commerciale - ' . $this->faker->words(3, true),
        ]);
    }
}