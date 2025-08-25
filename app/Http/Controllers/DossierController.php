<?php

namespace App\Http\Controllers;

use App\Models\{Dossier, Tiers};
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DossierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        // Paramètres de recherche et filtrage
        $search = $request->input('search');
        $statut = $request->input('statut');
        $type = $request->input('type');
        $mediateur_id = $request->input('mediateur_id');
        $perPage = $request->input('per_page', 15);

        // Construction de la requête
        $query = Dossier::forInertia();

        // Filtres
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('titre', 'like', "%{$search}%")
                  ->orWhere('descriptif', 'like', "%{$search}%");
            });
        }

        if ($statut === 'actifs') {
            $query->actifs();
        } elseif ($statut === 'clos') {
            $query->clos();
        } elseif (is_numeric($statut)) {
            $query->parStatut((int) $statut);
        }

        if ($type && is_numeric($type)) {
            $query->parType((int) $type);
        }

        if ($mediateur_id) {
            $query->parMediateur((int) $mediateur_id);
        }

        // Pagination avec tri
        $dossiers = $query->orderBy('saisine', 'desc')
                         ->paginate($perPage)
                         ->withQueryString();

        // Statistiques
        $stats = [
            'total' => Dossier::count(),
            'actifs' => Dossier::actifs()->count(),
            'clos' => Dossier::clos()->count(),
            'judiciaires' => Dossier::judiciaires()->count(),
            'recent_access' => Dossier::recentlyAccessed(24)->count(),
        ];

        // Options pour les filtres
        $mediateurs = Tiers::personnes()
                           ->whereHas('dossiersMediateur')
                           ->select('id', 'nom', 'prenom')
                           ->get()
                           ->map(fn($t) => $t->toSelectOption());

        return Inertia::render('Dossiers/Index', [
            'dossiers' => $dossiers,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'statut' => $statut,
                'type' => $type,
                'mediateur_id' => $mediateur_id,
            ],
            'mediateurs' => $mediateurs,
            'statuts' => [
                ['value' => 'all', 'label' => 'Tous'],
                ['value' => 'actifs', 'label' => 'Actifs'],
                ['value' => 'clos', 'label' => 'Clos'],
                ['value' => '1', 'label' => 'Ouvert'],
                ['value' => '2', 'label' => 'En cours'],
                ['value' => '9', 'label' => 'Clos'],
            ],
            'types' => [
                ['value' => '', 'label' => 'Tous types'],
                ['value' => '101', 'label' => 'Médiation Civile'],
                ['value' => '102', 'label' => 'Médiation Commerciale'],
                ['value' => '103', 'label' => 'Médiation Administrative'],
                ['value' => '104', 'label' => 'Médiation Familiale'],
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        // Médiateurs disponibles
        $mediateurs = Tiers::personnes()
                           ->select('id', 'nom', 'prenom')
                           ->orderBy('nom')
                           ->get()
                           ->map(fn($t) => $t->toSelectOption());

        // Centres disponibles
        $centres = Tiers::tribunaux()
                        ->select('id', 'denomination')
                        ->orderBy('denomination')
                        ->get()
                        ->map(fn($t) => $t->toSelectOption());

        return Inertia::render('Dossiers/Create', [
            'mediateurs' => $mediateurs,
            'centres' => $centres,
            'types' => [
                ['value' => 101, 'label' => 'Médiation Civile'],
                ['value' => 102, 'label' => 'Médiation Commerciale'],
                ['value' => 103, 'label' => 'Médiation Administrative'],
                ['value' => 104, 'label' => 'Médiation Familiale'],
                ['value' => 105, 'label' => 'Médiation Pénale'],
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|integer|in:101,102,103,104,105',
            'reference' => 'required|string|unique:dossiers,reference',
            'titre' => 'required|string|max:255',
            'descriptif' => 'nullable|string',
            'mediateur_id' => 'required|exists:tiers,id',
            'comediateur_id' => 'nullable|exists:tiers,id',
            'centre_id' => 'nullable|exists:tiers,id',
            'assistante_id' => 'nullable|exists:tiers,id',
            'saisine' => 'nullable|date',
        ]);

        // Valeurs par défaut
        $validated['statut'] = 1; // Ouvert
        $validated['saisine'] = $validated['saisine'] ?? now();

        $dossier = Dossier::create($validated);
        $dossier->updateAccess();

        return redirect()->route('dossiers.show', $dossier)
                        ->with('success', 'Dossier créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Dossier $dossier): Response
    {
        // Charger toutes les relations nécessaires
        $dossier->load([
            'mediateur',
            'comediateur',
            'centre', 
            'assistante',
            'dossierJudiciaire',
            'actions' => fn($q) => $q->latest()->limit(10),
            'documents' => fn($q) => $q->latest()->limit(10),
            'parties',
        ]);

        // Tracker l'accès
        $dossier->updateAccess();

        // Statistiques du dossier
        $stats = [
            'actions' => $dossier->actions()->count(),
            'documents' => $dossier->documents()->count(),
            'parties' => $dossier->parties()->count(),
            'duree' => $dossier->duree_traitement,
        ];

        return Inertia::render('Dossiers/Show', [
            'dossier' => $dossier,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dossier $dossier): Response
    {
        $dossier->load(['mediateur', 'comediateur', 'centre', 'assistante']);

        $mediateurs = Tiers::personnes()
                           ->select('id', 'nom', 'prenom')
                           ->get()
                           ->map(fn($t) => $t->toSelectOption());

        $centres = Tiers::tribunaux()
                        ->select('id', 'denomination')
                        ->get()
                        ->map(fn($t) => $t->toSelectOption());

        return Inertia::render('Dossiers/Edit', [
            'dossier' => $dossier,
            'mediateurs' => $mediateurs,
            'centres' => $centres,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dossier $dossier)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'descriptif' => 'nullable|string',
            'statut' => 'required|integer|in:1,2,3,4,9,99',
            'mediateur_id' => 'required|exists:tiers,id',
            'comediateur_id' => 'nullable|exists:tiers,id',
            'centre_id' => 'nullable|exists:tiers,id',
            'assistante_id' => 'nullable|exists:tiers,id',
            'saisine' => 'nullable|date',
            'cloture' => 'nullable|date',
        ]);

        // Logique métier pour la clôture
        if ($validated['statut'] == 9 && !$validated['cloture']) {
            $validated['cloture'] = now();
        }

        if ($validated['statut'] != 9) {
            $validated['cloture'] = null;
        }

        $dossier->update($validated);
        $dossier->updateAccess();

        return redirect()->route('dossiers.show', $dossier)
                        ->with('success', 'Dossier mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dossier $dossier)
    {
        // Vérifications avant suppression
        if ($dossier->actions()->exists() || $dossier->documents()->exists()) {
            return back()->withErrors(['error' => 'Impossible de supprimer un dossier avec des actions ou documents.']);
        }

        $dossier->delete();

        return redirect()->route('dossiers.index')
                        ->with('success', 'Dossier supprimé avec succès.');
    }

    /**
     * Clôturer un dossier
     */
    public function cloturer(Dossier $dossier)
    {
        $dossier->cloturer();

        return back()->with('success', 'Dossier clos avec succès.');
    }

    /**
     * Rouvrir un dossier
     */
    public function reouvrir(Dossier $dossier)
    {
        $dossier->reouvrir();

        return back()->with('success', 'Dossier rouvert avec succès.');
    }
}