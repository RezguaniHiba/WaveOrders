<?php
namespace App\Http\Controllers\Commercial;
use Illuminate\Support\Facades\DB; 
use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\User;
use App\Models\Client;
use Carbon\Carbon;

class CommercialController extends Controller
{
public function dashboard()
{
    $commercialId = auth()->id();
    // 1. Statistiques Clients
    $clientStats = [
        'total' => Client::where('commercial_id', $commercialId)->count(),
        'nouveaux' => Client::where('commercial_id', $commercialId)
            ->whereMonth('date_creation', now()->month)
            ->count(),
        'a_traiter' => Commande::where('commercial_id', $commercialId)
            ->whereIn('statut', [ 'brouillon',
                'en_cours_de_traitement', 
                'consignation',
                'partiellement_livree'])
            ->count()
    ];
    // 2. Nouveaux clients
    $nouveauxClients = Client::where('commercial_id', $commercialId)
        ->whereMonth('date_creation', now()->month)
        ->orderByDesc('date_creation')
        ->limit(5)
        ->get(['nom', 'date_creation']);

    // 3. Calcul des retards FIRST
    $retardsCount = Commande::where('commercial_id', $commercialId)
        ->where('date_livraison_prevue', '<', now())
        ->whereNotIn('statut', ['complètement_livree', 'annulee'])
        ->count();
    // 4. Calcul des impayés
    $impayesCount = Commande::where('commercial_id', $commercialId)
        ->whereNotIn('statut', ['brouillon', 'annulee'])
        ->whereRaw('(SELECT COALESCE(SUM(montant), 0) FROM reglements WHERE commande_id = commandes.id) < commandes.montant_ttc')
        ->count();
    // 5. Maintenant on construit $alertes
    $alertes = [
        'impayes' => $impayesCount,
        'retards' => $retardsCount,
        'total' => $impayesCount + $retardsCount
    ];
    // 6. Détail clients impayés
    $clientsImpayes = DB::table('clients')
        ->join('commandes', 'clients.id', '=', 'commandes.client_id')
        ->where('clients.commercial_id', $commercialId)
        ->whereNotIn('commandes.statut', ['brouillon'])
        ->select([
            'clients.nom',
            DB::raw('SUM(commandes.montant_ttc - COALESCE((SELECT SUM(montant) FROM reglements WHERE commande_id = commandes.id), 0)) as montant_du')
        ])
        ->groupBy('clients.id', 'clients.nom')
        ->having('montant_du', '>', 0)
        ->orderByDesc('montant_du')
        ->limit(3)
        ->get();
    // 7. Commandes en retard
    $commandesRetard = Commande::with('client:id,nom')
        ->where('commercial_id', $commercialId)
        ->where('date_livraison_prevue', '<', now())
        ->whereNotIn('statut', ['complètement_livree', 'brouillon'])
        ->orderBy('date_livraison_prevue')
        ->limit(3)
        ->get(['id', 'numero', 'client_id', 'date_livraison_prevue']);
    return view('commercial.dashboard', compact(
        'clientStats', 
        'nouveauxClients',
        'alertes',
        'clientsImpayes',
        'commandesRetard'
    ));
}
}