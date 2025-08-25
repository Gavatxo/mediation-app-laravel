<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\{Dossier, Tiers};
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class DossierTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function peut_creer_un_dossier()
    {
        $mediateur = Tiers::factory()->create();
        $dossier = Dossier::factory()->create([
            'mediateur_id' => $mediateur->id,
            'reference' => 'D2024-001',
            'titre' => 'Test médiation'
        ]);

        $this->assertEquals('D2024-001', $dossier->reference);
        $this->assertEquals('Test médiation', $dossier->titre);
        $this->assertEquals($mediateur->id, $dossier->mediateur_id);
        $this->assertTrue($dossier->is_actif);
    }

    #[Test]
    public function relations_tiers_fonctionnent()
    {
        $mediateur = Tiers::factory()->create(['nom' => 'Mediateur', 'prenom' => 'Test']);
        $comediateur = Tiers::factory()->create(['nom' => 'Comediateur', 'prenom' => 'Test']);
        
        $dossier = Dossier::factory()->create([
            'mediateur_id' => $mediateur->id,
            'comediateur_id' => $comediateur->id
        ]);

        $this->assertEquals('Test Mediateur', $dossier->mediateur->full_name);
        $this->assertEquals('Test Comediateur', $dossier->comediateur->full_name);
    }

    #[Test]
    public function scopes_fonctionnent()
    {
        // Créer différents types de dossiers
        Dossier::factory()->actif()->count(3)->create();
        Dossier::factory()->clos()->count(2)->create();
        
        $this->assertEquals(3, Dossier::actifs()->count());
        $this->assertEquals(2, Dossier::clos()->count());
        $this->assertEquals(5, Dossier::count());
    }

    #[Test]
    public function accessors_fonctionnent()
    {
        $dossier = Dossier::factory()->create([
            'type' => 101,
            'statut' => 2
        ]);

        $this->assertEquals('Médiation Civile', $dossier->type_label);
        $this->assertEquals('En cours', $dossier->statut_label);
        $this->assertTrue($dossier->is_actif);
        $this->assertNotNull($dossier->duree_traitement);
    }

    #[Test]
    public function peut_cloturer_dossier()
    {
        $dossier = Dossier::factory()->actif()->create();
        
        $this->assertTrue($dossier->is_actif);
        $this->assertNull($dossier->cloture);
        
        $dossier->cloturer();
        
        $this->assertFalse($dossier->fresh()->is_actif);
        $this->assertNotNull($dossier->fresh()->cloture);
        $this->assertEquals(9, $dossier->fresh()->statut);
    }

    #[Test]
    public function peut_reouvrir_dossier()
    {
        $dossier = Dossier::factory()->clos()->create();
        
        $this->assertFalse($dossier->is_actif);
        
        $dossier->reouvrir();
        
        $this->assertTrue($dossier->fresh()->is_actif);
        $this->assertNull($dossier->fresh()->cloture);
        $this->assertEquals(2, $dossier->fresh()->statut);
    }

    #[Test]
    public function trait_tracks_access_fonctionne()
    {
        $dossier = Dossier::factory()->create();

        $dossier->updateAccess(1);
        
        $this->assertEquals(1, $dossier->fresh()->acces_id);
        $this->assertNotNull($dossier->fresh()->acces_date);
        $this->assertTrue($dossier->fresh()->is_recently_accessed);

        // Test scopes
        $this->assertEquals(1, Dossier::accessedBy(1)->count());
        $this->assertEquals(1, Dossier::recentlyAccessed()->count());
    }

    #[Test]
    public function donnees_inertia_sont_correctes()
    {
        $dossier = Dossier::factory()->withAccess()->create([
            'type' => 101,
            'statut' => 2
        ]);

        $data = $dossier->toArray();

        $this->assertArrayHasKey('is_actif', $data);
        $this->assertArrayHasKey('type_label', $data);
        $this->assertArrayHasKey('statut_label', $data);
        $this->assertEquals('Médiation Civile', $data['type_label']);
        $this->assertEquals('En cours', $data['statut_label']);
    }

    #[Test]
    public function to_select_option_formate_correctement()
    {
        $dossier = Dossier::factory()->create([
            'reference' => 'D2024-001',
            'titre' => 'Test dossier',
            'type' => 101,
            'statut' => 2
        ]);

        $option = $dossier->toSelectOption();

        $this->assertEquals([
            'value' => $dossier->id,
            'label' => 'D2024-001 - Test dossier',
            'type' => 'Médiation Civile',
            'statut' => 'En cours',
            'isActif' => true,
        ], $option);
    }

    #[Test]
    public function to_summary_formate_correctement()
    {
        $mediateur = Tiers::factory()->create(['nom' => 'Dupont', 'prenom' => 'Jean']);
        $dossier = Dossier::factory()->create([
            'mediateur_id' => $mediateur->id,
            'reference' => 'D2024-001',
            'titre' => 'Test summary'
        ]);

        $summary = $dossier->toSummary();

        $this->assertEquals('D2024-001', $summary['reference']);
        $this->assertEquals('Jean Dupont', $summary['mediateur']);
        $this->assertArrayHasKey('is_actif', $summary);
        $this->assertArrayHasKey('duree', $summary);
    }
}