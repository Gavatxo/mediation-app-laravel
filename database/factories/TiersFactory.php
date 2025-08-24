<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tiers;

class TiersFactory extends Factory
{
    protected $model = Tiers::class;

    public function definition(): array
    {
        return [
            'reference' => $this->faker->unique()->numerify('T###'),
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'avatar' => null,
            'identifiant' => $this->faker->userName,
            'siret' => $this->faker->optional()->numerify('##############'),
        ];
    }

    /**
     * Factory pour une personne morale
     */
    public function personneMorale(): static
    {
        return $this->state(fn (array $attributes) => [
            'nom' => null,
            'prenom' => null,
            'denomination' => $this->faker->company,
        ]);
    }

    /**
     * Factory pour un tribunal
     */
    public function tribunal(): static
    {
        return $this->state(fn (array $attributes) => [
            'nom' => null,
            'prenom' => null,
            'denomination' => $this->faker->randomElement([
                'TGI Paris',
                'TGI Lyon',
                'Tribunal de Commerce de Paris',
                'Cour d\'Appel de Versailles'
            ]),
            'parent_id' => null,
        ]);
    }

    /**
     * Factory pour une chambre (avec parent)
     */
    public function chambre(?Tiers $tribunal = null): static
    {
        return $this->state(fn (array $attributes) => [
            'nom' => null,
            'prenom' => null,
            'denomination' => $this->faker->randomElement([
                '1ère Chambre Civile',
                '2ème Chambre Civile', 
                'Chambre Commerciale',
                'Chambre Sociale'
            ]),
            'parent_id' => $tribunal?->id ?? Tiers::factory()->tribunal(),
        ]);
    }

    /**
     * Factory avec suivi d'accès
     */
    public function withAccess(int $userId = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'acces_id' => $userId,
            'acces_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }
}