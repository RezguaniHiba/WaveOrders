<?php

namespace App\Http\Controllers\Commercial;

use App\Models\Commande;
use App\Models\Client;
use App\Models\Article;
use App\Models\LignesCommande;
use App\Models\HistoriqueCommande;
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
        'en_cours_de_traitement' => 'info',
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
            $client = Client::findOrFail($request->client_id);//        // Récupérer le client pour obtenir son commercial_id
            $commande = Commande::create([
                'numero' => $numero,
                'client_id' => $request->client_id,
                'commercial_id' => $client->commercial_id, 
                'cree_par' => $user->id,// utilisateur qui crée la commande 
                'date_livraison_prevue' => $request->date_livraison_prevue,
                'notes' => $request->notes,
                'statut' => 'brouillon',
            ]);
            foreach ($request->articles as $ligne) {
                $article = Article::lockForUpdate()->findOrFail($ligne['article_id']); // verrouillage BDD
                    // Déterminer le statut de la ligne
                $statutLigne = $ligne['statut'] ?? 'en_attente';
                // Vérification du stock selon le statut
                if ($statutLigne === 'reserve') {
                    if ($article->stock_disponible < $ligne['quantite']) {
                        throw new \Exception(
                            "Stock insuffisant pour réserver {$ligne['quantite']} unités de {$article->designation}. " .
                            "Stock disponible: {$article->stock_disponible}"
                        );
                    }
                }elseif ($statutLigne === 'en_consigne') {
                if ($article->stock_disponible < $ligne['quantite']) {
                    throw new \Exception(
                        "Stock insuffisant pour consigner {$ligne['quantite']} unités de {$article->designation}. " .
                        "Stock disponible: {$article->stock_disponible}"
                    );
                }
                }
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
        $commande = Commande::with(['lignesCommande', 'reglements'])->findOrFail($id); 
        $this->authorizeCommandeAccess($commande);
        if (!$commande->estModifiable() || $commande->statut === 'partiellement_livree') {
            return back()->withErrors('Action non autorisée : commande déjà livrée ou annulée.');
        }
        // Vérification des règlements existants
        if ($commande->reglements->isNotEmpty()) {
            return back()->with('error', 'Impossible d\'annuler : des règlements sont associés à cette commande.');
        }
            try {
            DB::beginTransaction();
            // Annulation des lignes de commande 
            $commande->lignesCommande()->update(['statut' => 'annule']);
            // Le trigger se chargera de mettre à jour le statut global de la commande
            // Historisation de l'action
            HistoriqueCommande::create([
                'commande_id' => $commande->id,
                'action' => 'annulation',
                'details' => 'Commande annulée via l\'interface'
            ]);
            DB::commit();
            return redirect()
                ->route('commandes.index')
                ->with('success', 'Commande annulée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Erreur lors de l\'annulation : ' . $e->getMessage());
        }
    }
    public function update(Request $request, $id)
{
    $commande = Commande::with(['lignesCommande', 'lignesCommande.article'])->findOrFail($id);
    $this->authorizeCommandeAccess($commande);

    if (!$commande->estModifiable()) {
        return redirect()->route('commandes.index')
            ->with('error', "Cette commande ne peut pas être modifiée (statut: {$commande->statut})");
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
        // Récupérer le nouveau client si changement
        $client = Client::findOrFail($request->client_id);
        $commande->update([
            'client_id' => $request->client_id,
             'commercial_id' => $client->commercial_id,
            'date_livraison_prevue' => $request->date_livraison_prevue,
            'notes' => $request->notes,
        ]);
        $idsEnvoyes = [];
        foreach ($request->articles as $ligneInput) {
            $article = Article::lockForUpdate()->findOrFail($ligneInput['article_id']);//on verrouille la ligne de l’article dans la BDD pendant la transaction 
            $nouveauStatut = $ligneInput['statut'];
            //si la ligne existe deja cad juste une modification
            if (!empty($ligneInput['id'])) {
                $ligne = LignesCommande::findOrFail($ligneInput['id']);
                $ancienStatut = $ligne->statut;
                // Vérification stock si changement de statut ou quantité
                if (in_array($nouveauStatut, ['reserve', 'en_consigne'])) {
                    $diffQuantite = $ligneInput['quantite'] - $ligne->quantite;
                    // Si on augmente la quantité OU si on passe d'un statut non consommateur à consommateur
                    if ($diffQuantite > 0 || !in_array($ancienStatut, ['reserve', 'en_consigne'])) {
                        if ($article->stock_disponible < max($diffQuantite, $ligneInput['quantite'])) {
                            throw new \Exception(
                                "Stock insuffisant pour {$nouveauStatut} ({$article->designation}) : ".
                                "disponible {$article->stock_disponible}"
                            );
                        }
                    }
                }
                $ligne->update([
                    'article_id' => $article->id,
                    'quantite' => $ligneInput['quantite'],
                    'prix_unitaire_ht' => $article->prix_ht,
                    'taux_tva' => $article->taux_tva,
                    'remise_percent' => $ligneInput['remise_percent'] ?? 0,
                    'statut' => $nouveauStatut,
                ]);
                $idsEnvoyes[] = $ligne->id;

            } else {
                // Nouvelle ligne
                if (in_array($nouveauStatut, ['reserve', 'en_consigne']) &&
                    $article->stock_disponible < $ligneInput['quantite']) {
                    throw new \Exception(
                        "Stock insuffisant pour {$nouveauStatut} ({$article->designation}) : " .
                        "disponible {$article->stock_disponible}"
                    );
                }
                $nouvelleLigne = LignesCommande::create([
                    'commande_id' => $commande->id,
                    'article_id' => $article->id,
                    'quantite' => $ligneInput['quantite'],
                    'prix_unitaire_ht' => $article->prix_ht,
                    'taux_tva' => $article->taux_tva,
                    'remise_percent' => $ligneInput['remise_percent'] ?? 0,
                    'statut' => $nouveauStatut,
                ]);
                $idsEnvoyes[] = $nouvelleLigne->id;
            }
        }

        // Suppression lignes absentes cad supprimer lors de la modif
        foreach ($commande->lignesCommande as $ligne) {//parcourt toutes les lignes existantes en BDD liées à la commande.
            if (!in_array($ligne->id, $idsEnvoyes)) {//cad a ete supprrimer
                if ($ligne->mouvements_stocks()->doesntExist()) {
                    if (in_array($ligne->statut, ['reserve', 'en_consigne'])) {
                        $ligne->update(['statut' => 'annule']);
                    } else {
                        $ligne->delete();
                    }
                }
            }
        }

        DB::commit();
        return redirect()->route('commandes.show', $commande->id)
            ->with('success', 'Commande mise à jour avec succès.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
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

