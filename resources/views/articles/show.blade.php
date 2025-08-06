@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Style général */
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
    
    /* Style pour les cartes d'information */
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
    
    /* Style pour le statut */
    .status-badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
</style>
@endpush

@section('title', 'Détails de l\'article')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Carte principale -->
    <div class="card border-0 shadow-lg-custom mb-6">
        <!-- En-tête -->
        <div class="card-header text-white header-gradient rounded-t-lg">
            <div class="flex justify-between items-center">
                <h3 class="mb-0">
                    <i class="fas fa-box-open fa-icon-text"></i>Fiche article : {{ $article->reference }}
                    <span class="status-badge badge rounded-pill {{ $article->actif ? 'bg-success' : 'bg-danger' }} float-end">
                        {{ $article->actif ? 'Actif' : 'Inactif' }}
                    </span>
                </h3>
            </div>
        </div>

        <!-- Corps de la carte -->
        <div class="card-body bg-gray-50">
            <!-- Section Informations article -->
            <div class="row g-4 mb-4">
                <!-- Carte Informations générales -->
                <div class="col-lg-6">
                    <div class="card info-card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-primary-emphasis">
                                <i class="fas fa-info-circle fa-icon-text"></i>Informations générales
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-muted"><i class="fas fa-barcode fa-icon-text"></i>Référence</h6>
                                <p class="mb-0 font-weight-bold">{{ $article->reference }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-muted"><i class="fas fa-tag fa-icon-text"></i>Désignation</h6>
                                <p class="mb-0">{{ $article->designation }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-muted"><i class="fas fa-code fa-icon-text"></i>Code WaveSoft</h6>
                                    <p class="mb-0">{{ $article->code_wavesoft ?? 'Non encore associé' }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-muted"><i class="fas fa-folder-tree fa-icon-text"></i>Famille</h6>
                                <p class="mb-0">{{ $article->famille?->cheminComplet() ?? 'Aucune' }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-muted"><i class="fas fa-align-left fa-icon-text"></i>Description</h6>
                                <p class="mb-0">{{ $article->description ?? 'Aucune description' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte Tarifs et stocks -->
                <div class="col-lg-6">
                    <div class="card info-card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-primary-emphasis">
                                <i class="fas fa-tags fa-icon-text"></i>Tarification & Stocks
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted"><i class="fas fa-money-bill-wave fa-icon-text"></i>Prix HT</h6>
                                    <p class="mb-0 font-weight-bold text-success">{{ number_format($article->prix_ht, 2, ',', ' ') }} DH</p>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted"><i class="fas fa-percent fa-icon-text"></i>Taux TVA</h6>
                                    <p class="mb-0 font-weight-bold">{{ $article->taux_tva }} %</p>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted"><i class="fas fa-money-bill-alt fa-icon-text"></i>Prix TTC</h6>
                                    <p class="mb-0 font-weight-bold text-primary">{{ number_format($article->prix_ttc, 2, ',', ' ') }} DH</p>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted"><i class="fas fa-balance-scale fa-icon-text"></i>Unité</h6>
                                    <p class="mb-0">{{ $article->unite ?? 'Non définie' }}</p>
                                </div>
                            </div>
                            
                            <hr class="my-3">
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="text-center p-2 bg-blue-50 rounded">
                                        <h6 class="text-muted"><i class="fas fa-box fa-icon-text"></i>Disponible</h6>
                                        <p class="mb-0 font-weight-bold text-primary">{{ $article->stock_disponible }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <div class="text-center p-2 bg-yellow-50 rounded">
                                        <h6 class="text-muted"><i class="fas fa-clock fa-icon-text"></i>Réservé</h6>
                                        <p class="mb-0 font-weight-bold text-warning">{{ $article->stock_reserve }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <div class="text-center p-2 bg-blue-100 rounded">
                                        <h6 class="text-muted"><i class="fas fa-exchange-alt fa-icon-text"></i>Consigné</h6>
                                        <p class="mb-0 font-weight-bold text-info">{{ $article->stock_consigne }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end small text-muted">
                                <i class="fas fa-clock fa-icon-text"></i>Dernière mise à jour : 
                                {{ $article->date_maj_stock ? $article->date_maj_stock->format('d/m/Y H:i') : 'Jamais' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left fa-icon-text"></i> Retour à la liste
                </a>
                <div class="btn-group">
                    <a href="{{ route('articles.edit', $article) }}" class="btn btn-primary" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); border: none;">
                        <i class="fas fa-pencil-alt fa-icon-text"></i> Modifier
                    </a>
                    <form action="{{ route('articles.destroy', $article) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger ms-2" 
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                            <i class="fas fa-trash-alt fa-icon-text"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection