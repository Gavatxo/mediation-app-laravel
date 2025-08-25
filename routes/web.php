<?php

use App\Http\Controllers\{ProfileController, TiersController, DossierController};
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/test', function () {
    return Inertia::render('Test', [
        'message' => 'Laravel + React + Inertia fonctionnent parfaitement !',
        'timestamp' => now()->format('d/m/Y H:i:s'),
        'env' => app()->environment(),
    ]);
})->name('test');

Route::middleware('auth')->group(function () {
    // Routes Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes Tiers 
    Route::resource('tiers', TiersController::class);

    // Routes Dossiers
    Route::resource('dossiers', DossierController::class);
    
    // Actions spécifiques Dossiers
    Route::patch('/dossiers/{dossier}/cloturer', [DossierController::class, 'cloturer'])->name('dossiers.cloturer');
    Route::patch('/dossiers/{dossier}/reouvrir', [DossierController::class, 'reouvrir'])->name('dossiers.reouvrir');
});

require __DIR__.'/auth.php';