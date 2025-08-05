<?php

namespace App\Http\Controllers\Commercial;

use App\Models\Commande;
use App\Models\Client;
use App\Models\Article;
use App\Models\LignesCommande;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommandeController extends Controller
{
    // liste les commandes selon le rôle de l’utilisateur, les filtres selon client et statut
   public function index(Request $request)
{
        $user = Auth::user();
        // Définition des statuts avec leurs couleurs associées 
         $statuts = [
        'brouillon' => 'secondary',
        'consignation' => 'warning',
        'reserve' => 'info',
        'partiellement_livree' => 'primary',
        'complètement_livree' => 'success',
        'annulee' => 'danger',
        ];

        $query = Commande::with('client')->orderByDesc('date_commande');
        if ($user->role === 'commercial') {
            $query->where(function ($q) use ($user) {
                $q->where('commercial_id', $user->id)
                  ->orWhere('cree_par', $user->id);
            });
        }
         // Filtres client & statut
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        //Paginer les résultats 15 par page et conserver les filtres dans les liens de pagination
        $commandes = $query->paginate(15)->appends($request->all());
        $clients = $user->role === 'admin'
            ? Client::all()
            : Client::where('commercial_id', $user->id)->get();
    // Envoi les commandes, les clients et les statuts vers la vue
    return view('commercial.commandes.index', compact('commandes', 'clients', 'statuts'));
}


    public function create()
    {
        $user = Auth::user();
        $clients = $user->role === 'admin'
            ? Client::all()
            : Client::where('commercial_id', $user->id)->get();
        $articles = Article::where('actif', 1)->get();
        return view('commercial.commandes.create', compact('clients', 'articles'));
     }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date_livraison_prevue' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'articles' => 'required|array|min:1',
            'articles.*.article_id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
            'articles.*.remise_percent' => 'nullable|numeric|min:0|max:100',
            'articles.*.statut' => 'sometimes|in:en_attente,reserve,en_consigne,prepare,livre,annule', 

        ],[
            'client_id.required' => 'Veuillez sélectionner un client.',
            'articles.required' => 'Veuillez ajouter au moins un article.',
            'articles.*.quantite.min' => 'La quantité doit être d\'au moins 1.',
        ]);

        try {
            DB::beginTransaction();
            //Génère un identifiant unique de commande.
            $numero = 'CMD-' . strtoupper(uniqid());
            $user = Auth::user();

            $commande = Commande::create([
                'numero' => $numero,
                'client_id' => $request->client_id,
                'commercial_id' => $user->role === 'commercial' ? $user->id : null,
                'cree_par' => $user->id,// utilisateur qui crée la commande 
                'date_livraison_prevue' => $request->date_livraison_prevue,
                'notes' => $request->notes,
                'statut' => 'brouillon',
            ]);
            foreach ($request->articles as $ligne) {
                $article = Article::findOrFail($ligne['article_id']);
                LignesCommande::create([
                    'commande_id' => $commande->id,
                    'article_id' => $article->id,
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire_ht' => $article->prix_ht,
                    'taux_tva' => $article->taux_tva,
                    'remise_percent' => $ligne['remise_percent'] ?? 0,
                    'statut' => $ligne['statut']  ?? 'en_attente',
                ]);
            }

            DB::commit();
            return redirect()->route('commandes.index')->with('success', 'Commande créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Erreur lors de la création : ' . $e->getMessage());
        }
    }
//POUR Afficher les détails d’une commande.
    public function show($id)
    {
        //Récupère la commande avec son client, ses lignes, et les articles liés
        $commande = Commande::with('client', 'lignesCommande.article')->findOrFail($id);
        $this->authorizeCommandeAccess($commande);

         // Définition des statuts avec leurs couleurs associées 
         $statuts = [
        'brouillon' => 'secondary',
        'consignation' => 'warning',
        'reserve' => 'info',
        'partiellement_livree' => 'primary',
        'complètement_livree' => 'success',
        'annulee' => 'danger',
        ];

        return view('commercial.commandes.show', compact('commande', 'statuts'));
    }

    public function edit($id)
    {
        $commande = Commande::with('lignesCommande')->findOrFail($id);
        $this->authorizeCommandeAccess($commande);
        // Interdire l'édition si commande deja livre ou bien annulee
        if (in_array($commande->statut, ['complètement_livree', 'annulee'])) {
            return redirect()->route('commandes.index')->withErrors("Cette commande ne peut pas être modifiée car elle est {$commande->statut}.");
        }
        // Si commercial : uniquement ses clients
        $user = Auth::user();
       $clients = $user->role === 'admin'
            ? Client::all()
            : Client::where('commercial_id', $user->id)->get();
        $articles = Article::where('actif', 1)->get();
        return view('commercial.commandes.edit', compact('commande', 'clients', 'articles'));
    }
    public function destroy($id)
    {
        $commande = Commande::findOrFail($id);
        $this->authorizeCommandeAccess($commande);
        if (in_array($commande->statut, ['complètement_livree', 'partiellement_livree','annulee'])) {
            return back()->withErrors('Action non autorisée : commande déjà livrée ou annulée.');
        }
        //MAJ de statut chaque ligne de commande
        foreach($commande->lignesCommande as $ligne){
            $ligne->update(['statut'=>'annule']);
        }
        return redirect()->route('commandes.index')->with('success', 'Commande annulée avec succés.');
    }

    public function update(Request $request, $id)
    {
        $commande = Commande::with('lignesCommande')->findOrFail($id);
        $this->authorizeCommandeAccess($commande);

        if (in_array($commande->statut, ['complètement_livree', 'annulee'])) {
            return redirect()->route('commandes.index')->withErrors("Cette commande ne peut pas être modifiée car elle est {$commande->statut}.");
        }
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date_livraison_prevue' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'articles' => 'required|array|min:1',
            'articles.*.article_id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
            'articles.*.remise_percent' => 'nullable|numeric|min:0|max:100',
            'articles.*.statut' => 'required|in:en_attente,reserve,en_consigne,prepare,livre,annule', 
        ]);
        try {
            DB::beginTransaction();
            // Mise à jour de la commande
            $commande->update([
                'client_id' => $request->client_id,
                'date_livraison_prevue' => $request->date_livraison_prevue,
                'notes' => $request->notes,

            ]);
            // Gérer les lignes
            $idsEnvoyes = [];
            foreach ($request->articles as $ligneInput) {
                if (!empty($ligneInput['id'])) {
                    // Mise à jour d'une ligne existante
                    $ligne = LignesCommande::findOrFail($ligneInput['id']);
                    $article = Article::findOrFail($ligneInput['article_id']);
                    $ligne->update([
                        'article_id' => $article->id,
                        'quantite' => $ligneInput['quantite'],
                        'prix_unitaire_ht' => $article->prix_ht,
                        'taux_tva' => $article->taux_tva,
                        'remise_percent' => $ligneInput['remise_percent'] ?? 0,
                        'statut' => $ligneInput['statut'], 
                    ]);
                    $idsEnvoyes[] = $ligne->id;
                } else {
                    // Création d'une nouvelle ligne
                    $article = Article::findOrFail($ligneInput['article_id']);
                    $nouvelleLigne = LignesCommande::create([
                        'commande_id' => $commande->id,
                        'article_id' => $article->id,
                        'quantite' => $ligneInput['quantite'],
                        'prix_unitaire_ht' => $article->prix_ht,
                        'taux_tva' => $article->taux_tva,
                        'remise_percent' => $ligneInput['remise_percent'] ?? 0, 
                        'statut' => $ligneInput['statut'],
                    ]);
                    $idsEnvoyes[] = $nouvelleLigne->id;
                }
            }

            // Supprimer les lignes absentes du formulaire et sans mouvements
            foreach ($commande->lignesCommande as $ligne) {
                if (!in_array($ligne->id, $idsEnvoyes)) {
                    if ($ligne->mouvements_stocks()->count() === 0) {
                        $ligne->delete();
                    }
                }
            }
            DB::commit();
            return redirect()->route('commandes.show', $commande->id)->with('success', 'Commande mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }
      /**
     * Vérifie que l'utilisateur actuel peut accéder à une commande
     */
    protected function authorizeCommandeAccess(Commande $commande)
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return true;
        }

        if ($commande->cree_par != $user->id && $commande->commercial_id != $user->id) {
            abort(403, 'Vous n’avez pas le droit d’accéder à cette commande.');
        }
    }
}

