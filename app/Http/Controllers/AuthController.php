<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Pour récupérer les données du formulaire
use App\Models\User; // Le modèle User qu’on a lié à la table utilisateurs
use Illuminate\Support\Facades\Auth; // Pour gérer la session utilisateur


class AuthController extends Controller
{
     public function showLoginForm()
    {
        // Retourne la vue login.blade.php
        return view('auth.login');
    }

     public function login(Request $request)
    {
         // Valide que l'email et le mot de passe sont bien fournis
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
         // Recherche l'utilisateur dans la base de données via l'email
        $user = User::where('email', $request->email)->first();
                // Si l'utilisateur n'existe pas OU que le mot de passe est incorrect
         if (!$user || !password_verify($request->password, $user->mot_de_passe_hash)) {
            // Redirige avec un message d'erreur
            return back()->withErrors(['email' => 'Identifiants incorrects']);
        }
        // Connecte l'utilisateur (création de la session),Laravel créera un cookie persistant si remember est coché.
        Auth::login($user, $request->has('remember'));
        return $user->role === 'admin'? redirect()->route('admin.dashboard') : redirect()->route('commercial.dashboard');
    }
     public function logout(Request $request)
    {
        // Déconnecte l'utilisateur
        Auth::logout();

        // Redirige vers la page de connexion
        return redirect('/login');
    }
}
