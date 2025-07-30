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
    $query = Commande::with('client')
        ->where('cree_par', auth()->id())
        ->orderByDesc('date_commande');

    // Filtrer par client si sélectionné
    if ($request->filled('client_id')) {
        $query->where('client_id', $request->client_id);
    }
    // Filtrer par statut si précisé
    if ($request->filled('statut')) {
        $query->where('statut', $request->statut);
    }
    $commandes = $query->paginate(15)->appends($request->all());

    // Récupérer les clients du commercial pour la liste déroulante
    $clients = Client::where('commercial_id', auth()->id())->get();

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

            $commande = Commande::create([
                'numero' => $numero,
                'client_id' => $request->client_id,
                'commercial_id' => Auth::user()->role === 'commercial' ? Auth::id() : null,
                'cree_par' => Auth::id(),
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
            return redirect()->route('commercial.commandes.index')->with('success', 'Commande créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Erreur lors de la création : ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $commande = Commande::with('client', 'lignesCommande.article')->findOrFail($id);

        if ($commande->cree_par != auth()->id()) {
            abort(403);
        }

        return view('commercial.commandes.show', compact('commande'));
    }

    public function edit($id)
    {
        $commande = Commande::findOrFail($id);

        if ($commande->cree_par != auth()->id() || $commande->statut != 'brouillon') {
            abort(403);
        }

        $clients = Client::where('commercial_id', auth()->id())->get();
        $articles = Article::where('actif', 1)->get();

        return view('commercial.commandes.edit', compact('commande', 'clients', 'articles'));
    }

    public function destroy($id)
    {
        $commande = Commande::findOrFail($id);

        if ($commande->cree_par != auth()->id() || in_array($commande->statut, ['livree', 'annulee'])) {
            return back()->withErrors('Action non autorisée.');
        }

        $commande->statut = 'annulee';
        $commande->save();

        return redirect()->route('commercial.commandes.index')->with('success', 'Commande annulée.');
    }
}
