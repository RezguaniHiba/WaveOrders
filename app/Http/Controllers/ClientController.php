<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Carbon;
use App\Models\User;


class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index(Request $request)
{
    $query = Client::query();
    // Filtre par commercial (pour les commerciaux)
    if (auth()->user()->role === 'commercial') {
        $query->where('commercial_id', auth()->id());
    }
    
    // Gestion de la recherche
    if ($request->filled('search')) {
        $searchTerm = trim(strtolower($request->search));    //La methode trim pour eliminer les espaces  et strtolower pour convertit la chaine en minuscule pour rendre la recherche insensible à la casse
        $query->where(function($q) use ($searchTerm) {
            $q->whereRaw('LOWER(nom) LIKE ?', ["%{$searchTerm}%"])
            ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchTerm}%"])
            ->orWhereRaw('LOWER(telephone) LIKE ?', ["%{$searchTerm}%"])
            ->orWhereRaw('LOWER(ville) LIKE ?', ["%{$searchTerm}%"]);
        });
    }

    // Filtre par commercial (pour les admins)
   if (auth()->user()->role === 'admin' && filled($request->commercial_id)) {
    $query->where('commercial_id', $request->commercial_id);
    }

    $clients = $query->with('utilisateur')->orderBy('nom')->paginate(15);
    $commerciaux = User::where('role', 'commercial')->where('actif', 1)->get();
    return view('clients.index', compact('clients', 'commerciaux'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $routePrefix = auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.';
        $commerciaux = [];
        if (auth()->user()->role === 'admin') {
            $commerciaux = User::where('role', 'commercial')->get();
        }
        return view('clients.create', compact('routePrefix', 'commerciaux'));
    }


    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $request->validate([
        'nom' => 'required|string|max:255',
        'email' => 'nullable|email',
        'telephone' => 'nullable|string|max:20',
        'adresse' => 'nullable|string',
        'ville' => 'nullable|string|max:100',
        'code_postal' => 'nullable|string|max:20',
        'pays' => 'nullable|string|max:100',
        'commercial_id' => 'nullable|exists:utilisateurs,id', // au cas où admin l’envoie
    ]);

    // Vérifie si un client avec le même email ou téléphone existe déjà
    $exists = Client::where(function ($query) use ($request) {
        if ($request->filled('email')) {
            $query->orWhere('email', $request->email);
        }
        if ($request->filled('telephone')) {
            $query->orWhere('telephone', $request->telephone);
        }
    })->exists();

    if ($exists) {
        return redirect()->back()
            ->withErrors(['email' => 'Un client avec cet email ou ce téléphone existe déjà.'])
            ->withInput();
    }
// Préparation des données
    $data = $request->all();
    $data['commercial_id'] = auth()->user()->role === 'admin'
        ? $request->input('commercial_id')
        : auth()->id();

    $data['date_creation'] = now(); // Carbon::now() est équivalent

    // Création
    Client::create($data);

    // Redirection
    $routePrefix = auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.';
    return redirect()->route($routePrefix . 'index')->with('success', 'Client créé avec succès.');
}

    /**
     * Display the specified resource.
     */
public function show(Client $client)
{
        // Vérification d'accès : un commercial ne peut voir que ses clients
    if (auth()->user()->role === 'commercial' && $client->commercial_id !== auth()->id()) {
        abort(403, 'Accès refusé');
    }
        $routePrefix = auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.';

    return view('clients.show', compact('client','routePrefix'));
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        // Vérification d'accès pour l'édition
        if (auth()->user()->role === 'commercial' && $client->commercial_id !== auth()->id()) {
            abort(403, 'Accès refusé');
        }
        $commerciaux = [];
        if (auth()->user()->role === 'admin') {
        $commerciaux = User::where('role', 'commercial')->where('actif', 1)->get();
        }
        $routePrefix = auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.';
         return view('clients.edit', compact('client', 'commerciaux', 'routePrefix'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        // Vérification d'accès pour la mise à jour
        if (auth()->user()->role === 'commercial' && $client->commercial_id !== auth()->id()) {
            abort(403, 'Accès refusé');
        }
        $rules=[
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string|max:100',
            'code_postal' => 'nullable|string|max:20',
            'pays' => 'nullable|string|max:100',
        ];
        if (auth()->user()->role === 'admin') {
            $rules['commercial_id'] = 'nullable|exists:utilisateurs,id';
        }
        $request->validate($rules);
        // Vérification : un autre client a-t-il le même email ou téléphone ?
        $exists = Client::where(function ($query) use ($request) {
                if ($request->filled('email')) {
                    $query->orWhere('email', $request->email);
                }
                if ($request->filled('telephone')) {
                    $query->orWhere('telephone', $request->telephone);
                }
            })->where('id', '!=', $id)->exists(); // Exclut le client actuel

        if ($exists) {
            return redirect()->back()
                ->withErrors(['email' => 'Un autre client utilise déjà cet email ou ce téléphone.'])
                ->withInput();
        }
        $data = $request->all();
        // Mise à jour du commercial_id uniquement si admin
        if (auth()->user()->role !== 'admin') {
            unset($data['commercial_id']); // empêche un commercial de changer ce champ
        }
        $data['date_maj'] = Carbon::now();
        $client->update($data);
        // Redirection selon rôle
        $routePrefix = auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.';
        return redirect()->route($routePrefix . 'index')->with('success', 'Client mis à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy($id)
    {
         $client = Client::with('commandes')->findOrFail($id);
        // Vérification d'accès pour la suppression
        if (auth()->user()->role === 'commercial' && $client->commercial_id !== auth()->id()) {
            abort(403, 'Accès refusé');
        }
        if ($client->commandes->count() > 0) {
            $countCommandes = $client->commandes()->count();    
            return back()->with('error', 
                "Suppression impossible : Ce client a $countCommandes commande(s) associée(s). " .
                "Pour des raisons de traçabilité, un client avec des commandes ne peut jamais être supprimé.");
            }
        $client->delete();
         // Redirection selon rôle
         $routePrefix = auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.';
    return redirect()->route($routePrefix . 'index')->with('success', 'Client supprimé.');
    }

}
