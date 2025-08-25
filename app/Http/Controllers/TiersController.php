<?php

namespace App\Http\Controllers;

use App\Models\Tiers;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TiersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        // Paramètres de recherche et filtrage
        $search = $request->input('search');
        $type = $request->input('type'); // 'personnes', 'juridictions', 'all'
        $perPage = $request->input('per_page', 15);

        // Construction de la requête
        $query = Tiers::forInertia();

        // Filtre par type
        match($type) {
            'personnes' => $query->personnes(),
            'juridictions' => $query->juridictions(),
            'tribunaux' => $query->tribunaux(),
            'chambres' => $query->chambres(),
            default => null // Tous
        };

        // Recherche textuelle
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('denomination', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        // Pagination avec tri
        $tiers = $query->orderBy('nom')
                      ->orderBy('denomination')
                      ->paginate($perPage)
                      ->withQueryString();

        // Statistiques pour le dashboard
        $stats = [
            'total' => Tiers::count(),
            'personnes' => Tiers::personnes()->count(),
            'juridictions' => Tiers::juridictions()->count(),
            'tribunaux' => Tiers::tribunaux()->count(),
            'recent_access' => Tiers::recentlyAccessed(24)->count(),
        ];

        return Inertia::render('Tiers/Index', [
            'tiers' => $tiers,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'type' => $type,
            ],
            'types' => [
                ['value' => 'all', 'label' => 'Tous'],
                ['value' => 'personnes', 'label' => 'Personnes'],
                ['value' => 'juridictions', 'label' => 'Juridictions'],
                ['value' => 'tribunaux', 'label' => 'Tribunaux'],
                ['value' => 'chambres', 'label' => 'Chambres'],
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        // Options pour les formulaires
        $tribunaux = Tiers::tribunaux()
                          ->select('id', 'denomination')
                          ->get()
                          ->map(fn($t) => $t->toSelectOption());

        return Inertia::render('Tiers/Create', [
            'tribunaux' => $tribunaux,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:personne,juridiction,tribunal',
            'nom' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'denomination' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:tiers,id',
            'reference' => 'nullable|string|unique:tiers,reference',
            'identifiant' => 'nullable|string|max:255',
            'siret' => 'nullable|string|max:14',
        ]);

        // Validation métier
        if ($validated['type'] === 'personne' && (!$validated['nom'])) {
            return back()->withErrors(['nom' => 'Le nom est requis pour une personne.']);
        }

        if (in_array($validated['type'], ['juridiction', 'tribunal']) && (!$validated['denomination'])) {
            return back()->withErrors(['denomination' => 'La dénomination est requise pour une juridiction.']);
        }

        // Nettoyage selon le type
        if ($validated['type'] === 'personne') {
            $validated['denomination'] = null;
            $validated['parent_id'] = null;
        } else {
            $validated['nom'] = null;
            $validated['prenom'] = null;
            if ($validated['type'] === 'tribunal') {
                $validated['parent_id'] = null;
            }
        }

        $tiers = Tiers::create($validated);

        // Tracking d'accès automatique
        $tiers->updateAccess();

        return redirect()->route('tiers.show', $tiers)
                        ->with('success', 'Tiers créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tiers $tiers): Response
    {
        // Charger les relations
        $tiers->load([
            'parent',
            'children',
            'dossiersMediateur' => fn($q) => $q->limit(5)->latest(),
            'dossiersComediateur' => fn($q) => $q->limit(5)->latest(),
        ]);

        // Tracker l'accès
        $tiers->updateAccess();

        // Statistiques du tiers
        $stats = [
            'dossiers_mediateur' => $tiers->dossiersMediateur()->count(),
            'dossiers_comediateur' => $tiers->dossiersComediateur()->count(),
            'dossiers_actifs' => $tiers->dossiersMediateur()->actifs()->count(),
            'enfants' => $tiers->children()->count(),
        ];

        return Inertia::render('Tiers/Show', [
            'tiers' => $tiers,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tiers $tiers): Response
    {
        $tribunaux = Tiers::tribunaux()
                          ->where('id', '!=', $tiers->id) // Éviter auto-référence
                          ->select('id', 'denomination')
                          ->get()
                          ->map(fn($t) => $t->toSelectOption());

        return Inertia::render('Tiers/Edit', [
            'tiers' => $tiers,
            'tribunaux' => $tribunaux,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tiers $tiers)
    {
        $validated = $request->validate([
            'nom' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'denomination' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:tiers,id',
            'reference' => 'nullable|string|unique:tiers,reference,' . $tiers->id,
            'identifiant' => 'nullable|string|max:255',
            'siret' => 'nullable|string|max:14',
        ]);

        // Validation métier selon le type existant
        if ($tiers->is_personne && !$validated['nom']) {
            return back()->withErrors(['nom' => 'Le nom est requis pour une personne.']);
        }

        if ($tiers->is_juridiction && !$validated['denomination']) {
            return back()->withErrors(['denomination' => 'La dénomination est requise pour une juridiction.']);
        }

        // Éviter l'auto-référence dans la hiérarchie
        if ($validated['parent_id'] === $tiers->id) {
            return back()->withErrors(['parent_id' => 'Un tiers ne peut pas être son propre parent.']);
        }

        $tiers->update($validated);
        $tiers->updateAccess();

        return redirect()->route('tiers.show', $tiers)
                        ->with('success', 'Tiers mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tiers $tiers)
    {
        // Vérifications avant suppression
        if ($tiers->dossiersMediateur()->exists()) {
            return back()->withErrors(['error' => 'Impossible de supprimer un tiers avec des dossiers en tant que médiateur.']);
        }

        if ($tiers->children()->exists()) {
            return back()->withErrors(['error' => 'Impossible de supprimer un tiers avec des enfants dans la hiérarchie.']);
        }

        $tiers->delete();

        return redirect()->route('tiers.index')
                        ->with('success', 'Tiers supprimé avec succès.');
    }
}