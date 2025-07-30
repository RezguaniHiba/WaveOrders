<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\Commercial\CommandeController;


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
// Admin uniquement
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Ajoute ici les routes spécifiques admin
});

// Commercial uniquement
Route::middleware(['auth', 'isCommercial'])->group(function () {
    Route::get('/commercial/dashboard', function () {
        return view('commercial.dashboard');
    })->name('commercial.dashboard');

    Route::get('/commercial/commandes', [CommandeController::class, 'index'])->name('commercial.commandes.index');
    Route::get('/commercial/commandes/create', [CommandeController::class, 'create'])->name('commandes.create');
    Route::post('/commercial/commandes', [CommandeController::class, 'store'])->name('commandes.store');

    Route::get('/commercial/commandes/{id}', [CommandeController::class, 'show'])->name('commandes.show');
Route::get('/commercial/commandes/{id}/edit', [CommandeController::class, 'edit'])->name('commandes.edit');
Route::delete('/commercial/commandes/{id}', [CommandeController::class, 'destroy'])->name('commandes.destroy');

});

