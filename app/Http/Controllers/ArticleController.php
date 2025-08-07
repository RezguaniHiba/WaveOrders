<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\FamillesArticle;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */

   public function index(Request $request)
{
    $query = Article::with('famille');
    if ($request->filled('q')) {
        $query->where(function($q) use ($request) {
            $q->where('reference', 'like', '%' . $request->q . '%')
            ->orWhere('designation', 'like', '%' . $request->q . '%');
        });
    }
    if ($request->filled('famille_id')) {
        $famille = FamillesArticle::find($request->famille_id);
        if ($famille) {
            $ids = $famille->getAllDescendantIds();
            $ids->push($famille->id); // Inclure la famille elle-même
            $query->whereIn('famille_id', $ids);
        }
    }

    $articles = $query->paginate(10);
    $familles = FamillesArticle::whereNull('parent_id')->with('enfants')->get(); // Pour l’arborescence

    return view('articles.index', compact('articles', 'familles'));
}

    /**
     * Show the form for creating a new resource.
     */
     public function create()
    {
        $familles = FamillesArticle::with('parent')->get();
        return view('articles.create',  [
            'article' => new Article(),
            'familles' => $familles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'code_wavesoft' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'prix_ht' => 'required|numeric',
            'taux_tva' => 'required|numeric',
            'unite' => 'nullable|string|max:255',
            'stock_disponible' => 'nullable|integer',
            'famille_id' => 'nullable|exists:familles_articles,id',
        ]);
        // Gestion de la checkbox 'actif'
        $validated['actif'] = $request->has('actif');
        Article::create($validated + [
            'date_maj_stock' => now(),
            'stock_reserve' => 0,
            'stock_consigne' => 0,
        ]);

        return redirect()->route('articles.index')->with('success', 'Article créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit(Article $article)
    {
        $familles = FamillesArticle::all();
        return view('articles.edit', compact('article', 'familles'));
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'code_wavesoft' => 'nullable|unique:articles,code_wavesoft,' . $article->id,
            'reference' => 'required|string|max:255|unique:articles,reference,' . $article->id,
            'designation' => 'required',
            'prix_ht' => 'required|numeric',
            'taux_tva' => 'required|numeric',
            'unite' => 'nullable|string',
            'famille_id' => 'nullable|exists:familles_articles,id',
        ]);

        $article->update($validated);
        return redirect()->route('articles.index')->with('success', 'Article mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Article supprimé avec succès.');
    }
}
