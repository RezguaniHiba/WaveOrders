<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Commande;
use App\Models\Reglement;
use Carbon\Carbon;
use App\Models\User;

class DashboardController extends Controller
{
        // Ajoutez cette méthode à votre contrôleur pour obtenir les stats des commerciaux
    protected function getSalesRepStats()
    {
        return [
            'totalCommerciaux' => User::where('role', 'commercial')->count(),
            'topCommerciaux' => User::where('role', 'commercial')
                ->withCount(['commandes as total_sales' => function($query) {
                     // On considère comme "validées" les commandes qui ne sont ni brouillon ni annulées
                $query->whereNotIn('statut', ['brouillon', 'annulee']);

                }])
                ->orderByDesc('total_sales')
                ->take(3)
                ->get()//Top 3 commerciaux : Affiche les 3 commerciaux avec le plus de commandes valides (tous statuts sauf "brouillon" et "annulée")
        ];
    }
    public function index()
    {
        // Statistiques 
        $stats = [
            'totalClients' => Client::count(),
            'newClientsThisMonth' => Client::whereMonth('date_creation', now()->month)->count(),
            'monthlyRevenue' => Commande::validatedThisMonth()->sum('montant_ttc'),
            'annualRevenue' => Commande::validatedThisYear()->sum('montant_ttc'),
        ];
        // Stats des commerciaux
        $commerciauxStats = $this->getSalesRepStats();
        // Données graphiques
        $revenueData = $this->getRevenueData();
        $clientAcquisitionData = $this->getClientAcquisitionData();
        // Calcul de la comparaison mensuelle
        $lastMonthRevenue = Commande::whereMonth('date_commande', now()->subMonth()->month)
            ->whereNotIn('statut', ['brouillon', 'annulee'])
            ->sum('montant_ttc');
        $monthlyDiff = $lastMonthRevenue ? 
            (($stats['monthlyRevenue'] - $lastMonthRevenue) / $lastMonthRevenue * 100) : 0;
         // Calcul de la comparaison annuelle
        $lastYearRevenue = Commande::whereYear('date_commande', now()->subYear()->year)
            ->whereNotIn('statut', ['brouillon', 'annulee'])
            ->sum('montant_ttc');
        $annualDiff = $lastYearRevenue ? 
            (($stats['annualRevenue'] - $lastYearRevenue) / $lastYearRevenue * 100) : 0;
        return view('admin.dashboard', compact(
            'stats',
            'commerciauxStats',
            'revenueData',
            'clientAcquisitionData',
            'monthlyDiff',
            'annualDiff' 
        ));
    }

    protected function getRevenueData()
    {
        $revenues = [];
        for ($i = 11; $i >= 0; $i--) {// Boucle sur les 12 derniers mois
            $month = now()->subMonths($i);// retire i months de mois actuel
            $revenues[] = [
                'month' => $month->format('M Y'),
                'amount' => Commande::whereYear('date_commande', $month->year)
                    ->whereMonth('date_commande', $month->month)
                    ->whereNotIn('statut', ['brouillon', 'annulee'])
                    ->sum('montant_ttc')
            ];
        }

        return $revenues;
    }

    protected function getClientAcquisitionData()
    {
        $acquisitions = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $acquisitions[] = [
                'month' => $month->format('M Y'),
                'count' => Client::whereYear('date_creation', $month->year)
                    ->whereMonth('date_creation', $month->month)
                    ->count()
            ];
        }

        return $acquisitions;
    }
}