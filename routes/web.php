<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResetPasswordController;

Route::get('/', function () {
    $nom="Gestion des Commandes";
    return view('welcome',[
       'nom' => $nom
    ]);
});
// Route vers le formulaire de connexion
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
// Traitement du formulaire de connexion (POST)
Route::post('/login', [AuthController::class, 'login']);

// Déconnexion
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


// Routes protégées par auth (accessible uniquement si connecté)
Route::middleware('auth')->group(function () {
    // Pour les admins
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Pour les commerciaux
    Route::get('/commercial/dashboard', function () {
        return view('commercial.dashboard');
    })->name('commercial.dashboard');
});

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
