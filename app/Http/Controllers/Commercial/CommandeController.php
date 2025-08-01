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
   public function index(Request $request)
{
        $user = Auth::user();
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
        $commandes = $query->paginate(15)->appends($request->all());
        $clients = $user->role === 'admin'
            ? Client::all()
            : Client::where('commercial_id', $user->id)->get();

        return view('commercial.commandes.index', compact('commandes', 'clients'));


}


    public function create()
    {
        $clients = Client::where('commercial_id', Auth::id())->get();
        $articles = Article::where('actif', 1)->get();

        return view('commercial.commandes.create', compact('clients', 'articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date_livraison_prevue' => 'nullable|date',
            'articles' => 'required|array|min:1',
            'articles.*.article_id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
            'articles.*.remise_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            $numero = 'CMD-' . strtoupper(uniqid());
            $user = Auth::user();

            $commande = Commande::create([
                'numero' => $numero,
                'client_id' => $request->client_id,
                'commercial_id' => $user->role === 'commercial' ? $user->id : null,
                'cree_par' => $user->id,
                'date_livraison_prevue' => $request->date_livraison_prevue,
                'statut' => 'brouillon'
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
                    'statut' => 'en_attente'
                ]);
            }

            DB::commit();
            return redirect()->route('commandes.index')->with('success', 'Commande créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Erreur lors de la création : ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $commande = Commande::with('client', 'lignesCommande.article')->findOrFail($id);
        $this->authorizeCommandeAccess($commande);

        return view('commercial.commandes.show', compact('commande'));
    }

    public function edit($id)
    {
        $commande = Commande::with('lignesCommande')->findOrFail($id);
        $this->authorizeCommandeAccess($commande);

        if ($commande->statut !== 'brouillon') {
            abort(403, 'Seules les commandes en brouillon peuvent être modifiées.');
        }

        $clients = Client::where('commercial_id', auth()->id())->get();
        $articles = Article::where('actif', 1)->get();

        return view('commercial.commandes.edit', compact('commande', 'clients', 'articles'));
    }

    public function destroy($id)
    {
        $commande = Commande::findOrFail($id);
        $this->authorizeCommandeAccess($commande);

        if (in_array($commande->statut, ['livree', 'annulee'])) {
            return back()->withErrors('Action non autorisée : commande déjà livrée ou annulée.');
        }

        $commande->update(['statut' => 'annulee']);

        return redirect()->route('commandes.index')->with('success', 'Commande annulée.');
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

