<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Tiers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class TiersTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function peut_creer_une_personne()
    {
        $personne = Tiers::factory()->create([
            'nom' => 'Dupont',
            'prenom' => 'Jean'
        ]);

        $this->assertTrue($personne->is_personne);
        $this->assertFalse($personne->is_juridiction);
        $this->assertEquals('Jean Dupont', $personne->full_name);
        $this->assertEquals('Personne', $personne->type_entity);
    }

    #[Test]
    public function peut_creer_un_tribunal()
    {
        $tribunal = Tiers::factory()->tribunal()->create();

        $this->assertTrue($tribunal->is_tribunal);
        $this->assertTrue($tribunal->is_juridiction);
        $this->assertFalse($tribunal->is_personne);
        $this->assertEquals('Tribunal', $tribunal->type_entity);
        $this->assertNotNull($tribunal->denomination);
    }

    #[Test]
    public function peut_creer_hierarchie_juridique()
    {
        $tribunal = Tiers::factory()->tribunal()->create(['denomination' => 'TGI Paris']);
        $chambre = Tiers::factory()->chambre($tribunal)->create(['denomination' => '1ère Chambre']);

        // Relations
        $this->assertEquals($tribunal->id, $chambre->parent_id);
        $this->assertTrue($tribunal->children->contains($chambre));
        
        // Hiérarchie
        $this->assertEquals('TGI Paris > 1ère Chambre', $chambre->getHierarchyPath());
        $this->assertEquals('Chambre', $chambre->type_entity);
    }

    #[Test]
    public function scopes_fonctionnent_correctement()
    {
        // Pas de truncate à cause des contraintes FK
        // Nettoyer proprement en respectant les relations
        \App\Models\Dossier::query()->delete();
        \App\Models\Tiers::query()->delete();
        
        // Créer des personnes physiques
        $personne1 = Tiers::factory()->create(['nom' => 'Dupont', 'prenom' => 'Jean']);
        $personne2 = Tiers::factory()->create(['nom' => 'Martin', 'prenom' => 'Marie']);
        
        // Créer un tribunal (juridiction racine)
        $tribunal = Tiers::factory()->tribunal()->create(['denomination' => 'TGI Paris']);
        
        // Créer des chambres (juridictions avec parent)
        $chambre1 = Tiers::factory()->chambre($tribunal)->create(['denomination' => '1ère Chambre']);
        $chambre2 = Tiers::factory()->chambre($tribunal)->create(['denomination' => '2ème Chambre']);

        // Vérifier les comptes
        $this->assertEquals(2, Tiers::personnes()->count(), 'Doit avoir 2 personnes');
        $this->assertEquals(3, Tiers::juridictions()->count(), 'Doit avoir 3 juridictions (1 tribunal + 2 chambres)');
        $this->assertEquals(1, Tiers::tribunaux()->count(), 'Doit avoir 1 tribunal');
        $this->assertEquals(2, Tiers::chambres()->count(), 'Doit avoir 2 chambres');
        
        // Vérifications de logique
        $this->assertTrue($personne1->is_personne);
        $this->assertTrue($tribunal->is_tribunal);
        $this->assertTrue($chambre1->is_juridiction);
        $this->assertFalse($chambre1->is_tribunal);
    }

    #[Test]
    public function trait_tracks_access_fonctionne()
    {
        $tiers = Tiers::factory()->create();

        // Test updateAccess
        $tiers->updateAccess(1);
        
        $this->assertEquals(1, $tiers->fresh()->acces_id);
        $this->assertNotNull($tiers->fresh()->acces_date);
        $this->assertTrue($tiers->fresh()->is_recently_accessed);

        // Test scopes
        $this->assertEquals(1, Tiers::accessedBy(1)->count());
        $this->assertEquals(1, Tiers::recentlyAccessed()->count());
    }

    #[Test]
    public function donnees_inertia_sont_correctes()
    {
        $tiers = Tiers::factory()->withAccess()->create([
            'nom' => 'Dupont',
            'prenom' => 'Jean'
        ]);

        $data = $tiers->toArray();

        // Vérifier que les appends sont inclus
        $this->assertArrayHasKey('full_name', $data);
        $this->assertArrayHasKey('type_entity', $data);
        $this->assertArrayHasKey('is_personne', $data);
        $this->assertEquals('Jean Dupont', $data['full_name']);
        $this->assertEquals('Personne', $data['type_entity']);
    }

    #[Test]
    public function to_select_option_formate_correctement()
    {
        $tiers = Tiers::factory()->create([
            'nom' => 'Dupont',
            'prenom' => 'Jean'
        ]);

        $option = $tiers->toSelectOption();

        $this->assertEquals([
            'value' => $tiers->id,
            'label' => 'Jean Dupont',
            'type' => 'Personne',
            'isJuridiction' => false,
        ], $option);
    }
}