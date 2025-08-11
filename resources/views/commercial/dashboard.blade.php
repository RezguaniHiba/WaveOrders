@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <!-- Message de bienvenue -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h2 class="mb-0">Bonjour {{ Auth::user()->nom }}</h2>
            </div>
        </div>
    </div>

    <!-- Cartes côte à côte -->
    <div class="row">
        <!-- Carte Clients -->
        <div class="col-lg-6 mb-4">
            <div class="card border-primary shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Mes Clients</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Total Clients -->
                        <div class="col-md-4 text-center border-end">
                            <div class="h2">{{ $clientStats['total'] }}</div>
                            <small class="text-muted">Clients</small>
                        </div>
                        
                        <!-- Nouveaux Clients -->
                        <div class="col-md-4 text-center border-end">
                            <div class="h2 text-success">{{ $clientStats['nouveaux'] }}</div>
                            <small class="text-muted">Nouveaux ce mois</small>
                        </div>
                        
                        <!-- Commandes à Traiter -->
                        <div class="col-md-4 text-center">
                            <div class="h2 text-warning">{{ $clientStats['a_traiter'] }}</div>
                            <small class="text-muted">Commandes à traiter</small>
                        </div>
                    </div>
                    
                    <!-- Liste déroulante des nouveaux clients -->
                    <div class="mt-3">
                        <a class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="collapse" href="#nouveauxClients">
                            Voir les nouveaux clients <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="collapse mt-2" id="nouveauxClients">
                            @foreach($nouveauxClients as $client)
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <span>{{ $client->nom }}</span>
                                <small class="text-muted">{{ $client->date_creation->format('d/m') }}</small>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Carte Alertes -->
        <div class="col-lg-6 mb-4">
            <div class="card border-danger shadow h-100">
                <div class="card-header bg-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Alertes</h6>
                        <span class="badge bg-white text-danger">{{ $alertes['total'] }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Impayés -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <strong><i class="fas fa-exclamation-circle text-danger me-2"></i> Clients avec impayés</strong>
                            <span class="badge bg-danger-light text-danger">{{ $alertes['impayes'] }}</span>
                        </div>
                        @foreach($clientsImpayes as $client)
                        <div class="ps-3 small">
                            {{ $client->nom }} 
                            <span class="float-end">{{ number_format($client->montant_du, 2) }} DH</span>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Retards -->
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <strong><i class="fas fa-clock text-warning me-2"></i> Retards livraison</strong>
                            <span class="badge bg-warning-light text-warning">{{ $alertes['retards'] }}</span>
                        </div>
                        @foreach($commandesRetard as $commande)
                        <div class="ps-3 small">
                            #{{ $commande->numero }} - {{ $commande->client->nom }}
                            <span class="float-end text-danger">
                                {{ now()->diffInDays($commande->date_livraison_prevue) }}j
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection