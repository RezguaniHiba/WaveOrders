<?php

namespace App\Http\Controllers;

use App\Models\Reglement;
use App\Models\Commande;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ReglementController extends Controller
{
    use AuthorizesRequests;
    /**
     * Affiche la liste des règlements.
     */
    public function index(Request $request)
    {
        $query = Reglement::with(['commande.client', 'utilisateur', 'clientPayeur'])
              ->latest();
        // Filtrage pour commerciaux
        if (auth()->user()->role === 'commercial') {
            $query->whereHas('commande', function($q) {
                $q->where('commercial_id', auth()->id())
                ->orWhere('cree_par', auth()->id());
            });
        }  
         // Filtre par commande si présent
        if ($request->has('commande_id')) {
            $commande = Commande::findOrFail($request->commande_id);
            $this->authorize('view', $commande);
            $query->where('commande_id', $commande->id);
        }
         return view('reglements.index', [
        'reglements' => $query->paginate(10),
        'commande' => $request->has('commande_id') ? $commande : null
    ]);
    }
     //Qui va récupérer les règlements lies a la commandes passe 
    public function parCommande(Commande $commande)
    {
        $user = Auth::user();        
        // Vérifier que le commercial a le droit de voir cette commande
        if ($user->role === 'commercial' && $commande->commercial_id !== $user->id && $commande->cree_par !== $user->id) {
                abort(403, "Vous n'avez pas accès à cette commande.");
        }
        // Charger les règlements liés à cette commande
        $reglements = Reglement::where('commande_id', $commande->id)
            ->with(['clientPayeur', 'utilisateur']) // charger relations utiles
            ->orderByDesc('date_reglement')
            ->paginate(10);
        return view('reglements.index', [
            'reglements' => $reglements,
            'commande' => $commande,
            'isFilteredView' => true
        ]);
    }

    /**
     * Affiche un règlement individuel.
     */
    public function show(Reglement $reglement)
    {
        $this->authorize('view', $reglement);
    
        $reglement->load(['commande.client', 'clientPayeur', 'utilisateur']);
        return view('reglements.show', compact('reglement'));
    }

    /**
     * Affiche le formulaire de création d'un règlement.
     */
public function create(Request $request, Commande $commande = null)
{
    // Si on vient d'une commande spécifique (route commandes.reglements.create)
    if ($commande) {
        $this->authorize('view', $commande);
        return view('reglements.create', [
            'commande' => $commande,
            'clients' => Client::orderBy('nom')->get()
        ]);
    }

    // Si on vient avec un paramètre commande_id (ancienne méthode)
    if ($request->has('commande_id')) {
        $commande = Commande::findOrFail($request->commande_id);
        $this->authorize('view', $commande);
        return view('reglements.create', [
            'commande' => $commande,
            'clients' => Client::orderBy('nom')->get()
        ]);
    }

    // Pour la liste générale (admin ou commercial)
    $commandes = [];
    if (auth()->user()->role === 'admin') {
        $commandes = Commande::with('reglements')
        ->where('statut', '!=', 'annulee')
        ->get()
        ->filter(fn($cmd) => $cmd->montant_restant > 0)//filter() est appliqué après récupération des modèles car montant restant est un att calculee non stocker dans la base
        ->sortByDesc('numero');
    } else {
        $commandes = Commande::where(function($query) {
                $query->where('commercial_id', auth()->id())
                      ->orWhere('cree_par', auth()->id());
            })
        ->where('statut', '!=', 'annulee')
        ->get()
        ->filter(fn($cmd) => $cmd->montant_restant > 0)
        ->sortByDesc('numero');
    }

    return view('reglements.create', [
        'commandes' => $commandes,
        'clients' => Client::orderBy('nom')->get()
    ]);
}

    /**
     * Enregistre un nouveau règlement.
     */
    public function store(Request $request)
{
   
    $validated = $request->validate([
        'commande_id' => 'required|exists:commandes,id',
        'montant' => [
            'required',
            'numeric',
            'min:0.01'],    
        'mode' => 'required|in:especes,cheque,carte_bancaire,virement,autre',
        'type_facturation' => 'required|in:facturer_client,client_payeur,autre',
        'date_reglement' => 'nullable|date',
        'client_payeur_id' => 'nullable|exists:clients,id',
        'commentaire' => 'nullable|string',
        'fichier_justificatif' => 'nullable|image|max:2048'
    ]);
      // Vérifier que l'utilisateur a accès à la commande
    $commande = Commande::findOrFail($validated['commande_id']);
    $this->authorize('view', $commande); 
    if ($request->hasFile('fichier_justificatif')) {
        $path = $request->file('fichier_justificatif')->store('reglements', 'public');
        $validated['fichier_justificatif'] = $path;
    }

    $validated['cree_par'] = auth()->id();
    // Si client_payeur_id est vide, utiliser le client de la commande
    if (empty($validated['client_payeur_id'])) {
        $validated['client_payeur_id'] = $commande->client_id;
    }
    Reglement::create($validated);

     // Redirection différente selon le workflow
    if ($request->has('from_commande')) {
        return redirect()->route('commandes.reglements.index', $commande->id)
            ->with('success', 'Règlement enregistré avec succès.');
    } else {
        return redirect()->route('reglements.index')
            ->with('success', 'Règlement enregistré avec succès.');
    }
}


    /**
     * Affiche le formulaire de modification.
     */
    public function edit(Reglement $reglement)
    {
        $this->authorize('update', $reglement);
        
        $reglement->load(['commande.client', 'clientPayeur']);
        $clients = Client::orderBy('nom')->get();
        
        return view('reglements.edit', [
            'reglement' => $reglement,
            'clients' => $clients
        ]);
    }
    /**
     * Met à jour un règlement.
     */
    public function update(Request $request, Reglement $reglement)
    {
        $this->authorize('update', $reglement);

        $validated = $request->validate([
            'montant' => 'required|numeric|min:0.01',
            'mode' => 'required|in:especes,cheque,carte_bancaire,virement,autre',
            'type_facturation' => 'required|in:facturer_client,client_payeur,autre',
            'date_reglement' => 'nullable|date',
            'client_payeur_id' => 'nullable|exists:clients,id',
            'commentaire' => 'nullable|string',
            'fichier_justificatif' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('fichier_justificatif')) {
            if ($reglement->fichier_justificatif && Storage::disk('public')->exists($reglement->fichier_justificatif)) {
                Storage::disk('public')->delete($reglement->fichier_justificatif);
            }
            $validated['fichier_justificatif'] = $request->file('fichier_justificatif')->store('reglements', 'public');
        }

        $reglement->update($validated);

         // Redirection cohérente avec le store()
            if (strpos(url()->previous(), 'commandes/')) {
                return redirect()->route('commandes.reglements.index', $reglement->commande_id)
                    ->with('success', 'Règlement mis à jour avec succès.');
            }

            return redirect()->route('reglements.index')
                ->with('success', 'Règlement mis à jour avec succès.');
        }

    /**
     * Supprime un règlement.
     */
    public function destroy(Reglement $reglement)
    {
        $this->authorize('delete', $reglement);
        $commandeId = $reglement->commande_id;
        if ($reglement->fichier_justificatif && Storage::disk('public')->exists($reglement->fichier_justificatif)) {
            Storage::disk('public')->delete($reglement->fichier_justificatif);
        }

        $reglement->delete();

       // Redirection cohérente
        if (strpos(url()->previous(), 'commandes/')) {
            return redirect()->route('commandes.reglements.index', $commandeId)
                ->with('success', 'Règlement supprimé avec succès.');
        }

        return redirect()->route('reglements.index')
            ->with('success', 'Règlement supprimé avec succès.');
    }
   

}  