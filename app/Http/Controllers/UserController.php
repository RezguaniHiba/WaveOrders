<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query(); 
        //recherche insensible a la casse
         if ($request->has('search')) {
            $search = strtolower($request->input('search'));
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nom) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(telephone) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(role) LIKE ?', ["%{$search}%"]);
            });
        }
          // Filtre par rôle
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }
        // Filtre par statut (actif/inactif)
        if ($request->filled('status')) {
            $isActive = $request->input('status') === 'active';
            $query->where('actif', $isActive);
        }
        // Conserver les paramètres de recherche dans la pagination

        $users = $query->paginate(10)->appends($request->query());
            return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:utilisateurs',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,commercial',
            'actif' => 'boolean',
            'mot_de_passe' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'role' => $request->role,
            'mot_de_passe_hash' => Hash::make($request->mot_de_passe),
            'actif' => true,
            'date_creation' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès');
    }
    /**
     * Display the specified resource.
     */
    public function show(User $user)
{
    return view('users.show', compact('user'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.create', compact('user'));//meme vue traite les deux cas creation/edition
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, User $user)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:utilisateurs,email,'.$user->id,
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,commercial',
            'actif' => 'boolean',
            'mot_de_passe' => 'nullable|string|min:8',//en mise a jour peut etre null si on le veut pas changer
            'mot_de_passe_confirmation' => ['required_with:mot_de_passe', 'same:mot_de_passe'],//obligatoire seulement si mot de passe rempli

        ]);
        $data = [
            'nom' => $request->nom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'role' => $request->role,
            'actif' => $request->has('actif'),
        ];
        if ($request->mot_de_passe) {
            $data['mot_de_passe_hash'] = Hash::make($request->mot_de_passe);
        }
        $user->update($data);
        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Vous ne pouvez pas vous supprimer vous-même.');
        }

        try {
            $user->delete();
            return redirect()->route('users.index')
                ->with('success', 'Utilisateur supprimé avec succès');
        } catch (\Illuminate\Database\QueryException $e) {
            // Code 23000 = violation de contrainte d'intégrité -> cle etrangere dans commandes 
            if ($e->getCode() == '23000') {
                // On désactive à la place
                $user->update(['actif' => 0]);
                return redirect()->route('users.index')
                    ->with('error', "Impossible de supprimer cet utilisateur car il est lié à des données (commandes, etc.). Il a été désactivé à la place.");
            }
            // Si c'est une autre erreur SQL, on la relance
            throw $e;
        }
    }

}
