<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Carbon;


class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->role === 'commercial') {
         // Le commercial ne voit que ses clients
        $clients = Client::where('commercial_id', auth()->id())
                         ->orderBy('nom')
                         ->paginate(15);
    } else {
        // L'admin voit tous les clients
        $clients = Client::orderBy('nom')
                         ->paginate(15);
    }

    return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $routePrefix = auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.';
        return view('clients.create', compact('routePrefix'));  
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
    ]);

    $data = $request->all();
    $data['commercial_id'] = auth()->id(); //  Ajout de l'ID du commercial connecté
    $data['date_creation'] = Carbon::now(); // ce champ est géré manuellement

    $client = Client::create($data);

    // Redirection selon rôle
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
    return view('clients.show', compact('client'));
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

        return view('clients.edit', compact('client')); 
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string|max:100',
            'code_postal' => 'nullable|string|max:20',
            'pays' => 'nullable|string|max:100',
        ]);

        $client = Client::findOrFail($id);

        // Vérification d'accès pour la mise à jour
        if (auth()->user()->role === 'commercial' && $client->commercial_id !== auth()->id()) {
            abort(403, 'Accès refusé');
        }

        $client->update($request->all());
            // Redirection selon rôle
        $routePrefix = auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.';
        return redirect()->route($routePrefix . 'index')->with('success', 'Client mis à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy($id)
    {
        $client = Client::findOrFail($id);
        // Vérification d'accès pour la suppression
        if (auth()->user()->role === 'commercial' && $client->commercial_id !== auth()->id()) {
            abort(403, 'Accès refusé');
        }
        $client->delete();
         // Redirection selon rôle
    $routePrefix = auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.';
    return redirect()->route($routePrefix . 'index')->with('success', 'Client supprimé.');
    }

}
