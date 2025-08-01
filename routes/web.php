<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\Commercial\CommandeController;
use App\Http\Controllers\ClientController;

Route::get('/', function () {
    return view('welcome');
})->name('home');
// Connexion
// Route vers le formulaire de connexion
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
// Traitement du formulaire de connexion (POST)
Route::post('/login', [AuthController::class, 'login']);

// Déconnexion
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Mot de passe oublié
// Affiche le formulaire
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');
// Traitement de l’envoi d’email
Route::post('/forgot-password', [App\Http\Controllers\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
// Affichage du formulaire de réinitialisation
Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');
// Traitement du nouveau mot de passe
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

//  Routes protégées par rôle
Route::middleware(['auth'])->group(function () {
    // Admin uniquement
    Route::middleware(['auth', 'isAdmin'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // --- Routes pour gestion des Clients par l'admin ---
        Route::prefix('/admin/clients')->name('admin.clients.')->group(function () {
            Route::get('/', [ClientController::class, 'index'])->name('index');
            Route::get('/create', [ClientController::class, 'create'])->name('create');
            Route::post('/', [ClientController::class, 'store'])->name('store');
            Route::get('/{client}', [ClientController::class, 'show'])->name('show');
            Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
            Route::put('/{client}', [ClientController::class, 'update'])->name('update');
            Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
        });

    });

    // Commercial uniquement
    Route::middleware(['auth', 'isCommercial'])->group(function () {
        Route::get('/commercial/dashboard', function () {
            return view('commercial.dashboard');
        })->name('commercial.dashboard');
        // --- Routes pour gestion des Clients ---
    Route::prefix('/commercial/clients')->name('clients.')->group(function () {
            Route::get('/', [ClientController::class, 'index'])->name('index');
            Route::get('/create', [ClientController::class, 'create'])->name('create');
            Route::post('/', [ClientController::class, 'store'])->name('store');
            Route::get('/{client}', [ClientController::class, 'show'])->name('show');
            Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
            Route::put('/{client}', [ClientController::class, 'update'])->name('update');
            Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
        });
    });

// --- Commandes (accessibles aux deux rôles) ---
    Route::resource('commandes', CommandeController::class);
});