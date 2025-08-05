@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Style*/
    .card {
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    /* Ombre plus prononcée */
    .shadow-lg-custom {
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    
    /* Style des badges */
    .badge {
        font-size: 0.9rem;
        padding: 0.4em 0.7em;
    }
    
    /* Effet de survol pour les lignes du tableau */
    .table-hover tbody tr:hover {
        background-color: rgba(30, 58, 138, 0.05);
    }
    
    /* Style des boutons */
    .btn {
        font-size: 1rem;
        padding: 0.5rem 1.5rem;
        border-radius: 0.5rem;
    }
    
    /* Espacement entre icônes et texte */
    .fa-icon-text {
        margin-right: 0.6rem;
    }
    
    /* En-tête coloré */
    .header-gradient {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    }
    
    /* Style pour les cartes d'information comme dans la fiche commande */
    .info-card {
        border-radius: 0.5rem;
        height: 100%;
    }
    
    .info-card .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        background-color: white;
    }
    
    .info-card .card-header h5 {
        font-size: 1.3rem;
        color: #1e40af;
    }
    
    .info-card .text-muted {
        font-size: 0.95rem;
        color: #6b7280 !important;
    }
    
    .info-card .font-weight-bold {
        font-weight: 600 !important;
    }
    
    /* Style pour le tableau */
    .table thead th {
        font-size: 1rem;
    }
    
    .table td, .table th {
        padding: 0.8rem 0.6rem;
    }
    
    /* Boutons d'action */
    .action-buttons .btn {
        border-radius: 50rem !important;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Carte principale -->
    <div class="card border-0 shadow-lg-custom mb-6">
        <!-- En-tête -->
        <div class="card-header text-white header-gradient rounded-t-lg">
            <div class="flex justify-between items-center">
                <h3 class="mb-0">Fiche du client <strong>"{{ $client->nom }}"</strong></h3>
            </div>
        </div>

        <!-- Corps de la carte -->
        <div class="card-body bg-gray-50">
            <!-- Section Informations client -->
            <div class="row g-4 mb-4">
                <!-- Carte Informations personnelles -->
                <div class="col-lg-12">
                    <div class="card info-card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-primary-emphasis">
                                <i class="fas fa-user-circle fa-icon-text"></i>Informations personnelles
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Colonne Nom -->
                                <div class="col-md-3 mb-3">
                                    <h6 class="text-muted"><i class="fas fa-user fa-icon-text"></i>Nom complet</h6>
                                    <p class="mb-0 font-weight-bold">{{ $client->nom }}</p>
                                </div>
                                
                                <!-- Colonne Email -->
                                <div class="col-md-3 mb-3">
                                    <h6 class="text-muted"><i class="fas fa-envelope fa-icon-text"></i>Email</h6>
                                    <p class="mb-0">{{ $client->email ?? 'Non renseigné' }}</p>
                                </div>
                                
                                <!-- Colonne Téléphone -->
                                <div class="col-md-3 mb-3">
                                    <h6 class="text-muted"><i class="fas fa-phone fa-icon-text"></i>Téléphone</h6>
                                    <p class="mb-0">{{ $client->telephone ?? 'Non renseigné' }}</p>
                                </div>
                                
                                <!-- Colonne Adresse -->
                                <div class="col-md-3 mb-3">
                                    <h6 class="text-muted"><i class="fas fa-map-marker-alt fa-icon-text"></i>Adresse</h6>
                                    <p class="mb-0">{{ $client->adresse ?? '—' }}</p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Colonne Ville -->
                                <div class="col-md-3 mb-3">
                                    <h6 class="text-muted"><i class="fas fa-city fa-icon-text"></i> Ville</h6>
                                    <p class="mb-0">{{ $client->ville ?? '—' }}</p>
                                </div>
                                
                                <!-- Colonne Code postal -->
                                <div class="col-md-3 mb-3">
                                    <h6 class="text-muted"><i class="fas fa-mail-bulk fa-icon-text"></i> Code postal</h6>
                                    <p class="mb-0">{{ $client->code_postal ?? '—' }}</p>
                                </div>
                                
                                <!-- Colonne Pays -->
                                <div class="col-md-3 mb-3">
                                    <h6 class="text-muted"><i class="fas fa-globe fa-icon-text"></i> Pays</h6>
                                    <p class="mb-0">{{ $client->pays }}</p>
                                </div>
                                 @if(auth()->user()->role === 'admin')
                                    <div class="col-md-3 mb-3">
                                        <h6 class="text-muted"><i class="fas fa-user-tie fa-icon-text"></i>Commercial</h6>
                                        <p class="mb-0">{{ $client->utilisateur->nom ?? 'Non attribué' }}</p>
                                    </div>
                                @else
                                    <div class="col-md-3"></div>
                                @endif
                                <!-- Colonne vide pour l'alignement -->
                                <div class="col-md-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Commandes liées dans une carte -->
            <div class="card info-card mb-6">
                <div class="card-header">
                    <h5 class="mb-0 text-primary-emphasis">
                        <i class="fas fa-shopping-cart fa-icon-text"></i>Commandes liées
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if ($client->commandes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="table table-striped table-hover table-bordered border mb-0">
                                <thead class="bg-black-50 text-primary-emphasis">
                                    <tr>
                                        <th class="px-4 py-3 text-center">#</th>
                                        <th class="px-4 py-3 text-center">Numéro</th>
                                        <th class="px-4 py-3 text-center">Date</th>
                                        <th class="px-4 py-3 text-center">Montant TTC</th>
                                        <th class="px-4 py-3 text-center">Statut</th>
                                        <th class="px-4 py-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($client->commandes as $commande)
                                        <tr class="border-t hover:bg-blue-50">
                                            <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                                            <td class="px-4 py-3 text-center">{{ $commande->numero }}</td>
                                            <td class="px-4 py-3 text-center">{{ $commande->date_commande->format('d/m/Y') }}</td>
                                            <td class="px-4 py-3 text-center">{{ number_format($commande->montant_ttc, 2, ',', ' ') }} DH</td>
                                            <td class="px-4 py-3 text-center">
                                                @php
                                                    $statusColors = [ 
                                                        'brouillon' => 'secondary',
                                                        'consignation' => 'warning',
                                                        'reserve' => 'info',
                                                        'partiellement_livree' => 'primary',
                                                        'complètement_livree' => 'success',
                                                        'annulee' => 'danger'
                                                    ];
                                                @endphp
                                                <span class="badge rounded-pill bg-{{ $statusColors[$commande->statut] ?? 'dark' }}" title="Statut : {{ ucfirst($commande->statut) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <a href="{{ route('commandes.show', $commande->id) }}" 
                                                   class="text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-eye fa-icon-text"></i>Détails de la commande
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-box-open fa-3x mb-4 text-gray-300"></i>
                            <p class="text-lg">Aucune commande pour ce client.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="mt-4 d-flex justify-content-between action-buttons">
                <a href="{{ route($routePrefix . 'index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left fa-icon-text"></i> Retour à la liste
                </a>
                <a href="{{ route((auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.') . 'edit', $client->id) }}" 
                   class="btn btn-primary" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); border: none;">
                    <i class="fas fa-pencil-alt fa-icon-text"></i> Modifier les informations
                </a>
            </div>
        </div>
    </div>
</div>
@endsection