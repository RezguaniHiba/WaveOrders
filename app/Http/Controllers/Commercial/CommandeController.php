<?php

namespace App\Http\Controllers\Commercial;
use App\Models\Commande;
use App\Models\Client;
use App\Models\Article;
use App\Models\LignesCommande;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommandeController extends Controller
{
    public function index()
{
    $commandes = \App\Models\Commande::with('client')
        ->where('cree_par', auth()->id())
        ->orderByDesc('date_commande')
        ->get();

    return view('commercial.commandes.index', compact('commandes'));
}
public function create()
{
    // Récupérer les clients assignés au commercial connecté
    $clients = Client::where('commercial_id', Auth::id())->get();

    // Tous les articles actifs
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

        // Numéro unique
        $numero = 'CMD-' . strtoupper(uniqid());

        // Créer la commande (montants = 0, calculés par trigger)
        $commande = Commande::create([
            'numero' => $numero,
            'client_id' => $request->client_id,
            'commercial_id' => Auth::user()->role === 'commercial' ? Auth::id() : null,
            'cree_par' => Auth::id(),
            'date_livraison_prevue' => $request->date_livraison_prevue,
            'statut' => 'brouillon'
        ]);

        // Lignes de commande
        foreach ($request->articles as $ligne) {
            $article = Article::findOrFail($ligne['article_id']);
            LigneCommande::create([
                'commande_id' => $commande->id,
                'article_id' => $article->id,
                'quantite' => $ligne['quantite'],
                'prix_unitaire_ht' => $article->prix_ht,
                'taux_tva' => $article->taux_tva,
                'remise_percent' => $ligne['remise_percent'] ?? 0,
                'statut' => 'en_attente'
            ]);
            // Pas besoin de recalculer, le trigger le fait automatiquement
        }

        DB::commit();
        return redirect()->route('commercial.commandes.index')->with('success', 'Commande créée avec succès.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors('Erreur lors de la création : ' . $e->getMessage());
    }
}



}
