<?php

namespace App\Http\Controllers;

use App\Models\FamillesArticle;
use Illuminate\Http\Request;

class FamillesArticleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        // On charge toutes les familles avec enfants et articles
        $familles = FamillesArticle::withCount('articles')
        ->with('enfants')
        ->get(); 
        if ($search) {
        // On filtre uniquement celles (ou leurs descendants) qui correspondent au search
         $familles = $familles->filter(function ($famille) use ($search) {
            return str_contains(strtolower($famille->libelle), strtolower($search)) ||
                   str_contains(strtolower($famille->code_wavesoft), strtolower($search)) ||
                   $famille->hasDescendantMatching($search);
            });
        }
//Recherche dans toute l’arborescence mais n’affiche que les racines	
        // On ne garde que les familles racines pour l'affichage
        $famillesRacines = $familles->whereNull('parent_id');
        return view('familles_articles.index', compact('famillesRacines', 'search'));
    }

    public function create()
    {
        $familles = FamillesArticle::all();
        return view('familles_articles.create', compact('familles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:100',
            'code_wavesoft' => 'nullable|string|max:20',
            'parent_id' => 'nullable|exists:familles_articles,id',
        ]);

        FamillesArticle::create($request->all());

        return redirect()->route('familles-articles.index')->with('success', 'Famille ajoutée avec succès.');
    }

    public function edit($id)
    {
        $famille = FamillesArticle::findOrFail($id);
        $familles = FamillesArticle::where('id', '!=', $id)->get(); // éviter boucle
        return view('familles_articles.edit', compact('famille', 'familles'));
    }

    public function update(Request $request, FamillesArticle $famille)
    {
        //avec injection de modele on n'a pas besoin de faire FamillesArticle::findOrFail($id) => laravel l'injecte automatiq. la famille grace a l'ID dabs l'url
            $validated = $request->validate([
            'libelle' => 'required|string|max:100',
            'code_wavesoft' => 'nullable|string|max:20',
            'parent_id' => 'nullable|exists:familles_articles,id',
        ]);

        // Éviter qu'une famille devienne son propre parent ou un de ses enfants
        if ($validated['parent_id'] == $famille->id || $famille->getAllDescendantIds()->contains($validated['parent_id'])) {
            return back()->withErrors(['parent_id' => 'La famille parente ne peut pas être elle-même ou un de ses descendants.']);
        }

        $famille->update($validated);

        return redirect()->route('familles-articles.index')->with('success', 'Famille mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $famille = FamillesArticle::findOrFail($id);

        // Sécurité : on interdit la suppression si la famille a des sous-familles ou articles
        if ($famille->enfants()->count() > 0) {
            return redirect()->route('familles-articles.index')
                ->with('error', 'Impossible de supprimer cette famille : elle contient des sous-familles.');
        }
        // Vérifie si la famille a des articles
        if ($famille->articles()->count() > 0) {
            return redirect()->route('familles-articles.index')
                ->with('error', 'Impossible de supprimer cette famille : elle contient des articles.');
        }
        $famille->delete();
        return redirect()->route('familles-articles.index')->with('success', 'Famille supprimée avec succès.');
    }

    public function show($id)
    {
        $famille = FamillesArticle::findOrFail($id);
        $enfants = $famille->enfants;
        $articles = $famille->articles;
        return view('familles_articles.show', compact('famille', 'enfants', 'articles'));
    }
    
}
