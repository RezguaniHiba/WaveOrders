@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .bg-light-blue {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    }
    
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
    
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        background-color: white;
    }
    
    .shadow-lg-custom {
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    
    .fa-icon-text {
        margin-right: 0.6rem;
    }
    
    .badge {
        font-size: 0.9rem;
        padding: 0.4em 0.7em;
        font-weight: 500;
    }
    
    .bg-gray-50 {
        background-color: #f9fafb;
    }
    
    .text-primary-emphasis {
        color: #1e3a8a;
    }
    
    .whitespace-pre-line {
        white-space: pre-line;
    }
    
    /* Style des boutons */
    .btn {
        font-size: 1rem;
        padding: 0.5rem 1.5rem;
        border-radius: 0.5rem;
    }
    
    .info-row {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    
    .info-item {
        flex: 1;
        min-width: 200px;
        margin-bottom: 0.5rem;
        padding-right: 1rem;
    }
    
    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        font-weight: 500;
    }
    
    .text-primary-value {
        color: #1e3a8a;
        font-weight: bold;
    }
    
    .border-right {
        border-right: 1px solid #e9ecef;
    }
    
    @media (max-width: 768px) {
        .info-item {
            flex: 100%;
            padding-right: 0;
        }
        .border-right {
            border-right: none;
        }
    }
</style>
@endpush

@section('title', 'Détail Règlement - #'.$reglement->id)

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-lg-custom">
        <!-- En-tête de la carte -->
        <div class="card-header text-white bg-light-blue py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="h5 mb-0">
                    <i class="fas fa-money-bill-wave fa-icon-text"></i>
                    Détail du règlement #{{ $reglement->id }}
                </h3>
                <span class="badge bg-light text-dark">
                    {{ $reglement->commande->statut === 'payee' ? 'Commande payée' : 'Commande en cours' }}
                </span>
            </div>
        </div>

        <div class="card-body bg-gray-50">
            <!-- Cartes Informations -->
            <div class="row g-4 mb-4">
                <!-- Carte Informations générales -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-primary-emphasis">
                                <i class="fas fa-info-circle fa-icon-text"></i>Informations générales
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-row">
                                <div class="info-item border-right">
                                    <div class="info-label">
                                        <i class="fas fa-file-invoice fa-icon-text"></i>Commande associée
                                    </div>
                                    <div class="info-value">#{{ $reglement->commande->numero }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="far fa-calendar-alt fa-icon-text"></i>Date du règlement
                                    </div>
                                    <div class="info-value">{{ $reglement->date_reglement->format('d/m/Y') }}</div>
                                </div>
                            </div>
                            
                            <div class="info-row">
                                <div class="info-item border-right">
                                    <div class="info-label">
                                        <i class="fas fa-money-bill-wave fa-icon-text"></i>Montant
                                    </div>
                                    <div class="info-value text-primary-value">{{ number_format($reglement->montant, 2, ',', ' ') }} DH</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-credit-card fa-icon-text"></i>Mode de paiement
                                    </div>
                                    <div class="info-value">
                                        <span class="badge bg-{{
                                            ['especes' => 'primary',
                                             'cheque' => 'success',
                                             'carte_bancaire' => 'info',
                                             'virement' => 'secondary',
                                             'autre' => 'light text-dark'][$reglement->mode]
                                        }}">
                                            {{ $reglement->mode_libelle }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="info-row">
                                <div class="info-item border-right">
                                    <div class="info-label">
                                        <i class="fas fa-user-clock fa-icon-text"></i>Créé par
                                    </div>
                                    <div class="info-value">{{ $reglement->utilisateur->nom }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-sync-alt fa-icon-text"></i>Dernière modification
                                    </div>
                                    <div class="info-value">{{ $reglement->updated_at->format('d/m/Y à H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte Clients -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-primary-emphasis">
                                <i class="fas fa-users fa-icon-text"></i>Informations clients
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-row">
                                <div class="info-item border-right">
                                    <div class="info-label">
                                        <i class="fas fa-user-tag fa-icon-text"></i>Client commande
                                    </div>
                                    <div class="info-value font-weight-bold">{{ $reglement->commande->client->nom }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-user-check fa-icon-text"></i>Client payeur
                                    </div>
                                    <div class="info-value font-weight-bold">{{ $reglement->clientPayeur->nom }}</div>
                                </div>
                            </div>
                            
                            <div class="info-row">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-receipt fa-icon-text"></i>Type de facturation
                                    </div>
                                    <div class="info-value">{{ $reglement->type_facturation_libelle }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Justificatif et Commentaire -->
            <div class="row g-4">
                <!-- Justificatif -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-primary-emphasis">
                                <i class="fas fa-file-alt fa-icon-text"></i>Justificatif
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($reglement->fichier_justificatif)
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-file-pdf text-danger fs-3 me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Document justificatif</h6>
                                        <p class="small text-muted mb-0">
                                            {{ basename($reglement->fichier_justificatif) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ Storage::url($reglement->fichier_justificatif) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary btn-rounded">
                                        <i class="fas fa-eye fa-icon-text"></i> Voir
                                    </a>
                                    <a href="{{ Storage::url($reglement->fichier_justificatif) }}" 
                                       download 
                                       class="btn btn-sm btn-outline-secondary btn-rounded">
                                        <i class="fas fa-download fa-icon-text"></i> Télécharger
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-light text-center py-3">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    Aucun justificatif associé à ce règlement
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @if($reglement->commentaire)
                <!-- Commentaire -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-primary-emphasis">
                                <i class="fas fa-comment fa-icon-text"></i>Commentaire
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="p-3 bg-light rounded">
                                <p class="mb-0 whitespace-pre-line">{{ $reglement->commentaire }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <!-- Boutons d'action -->
            <div class="card-footer bg-transparent border-top-0 py-3">
                <div class="d-flex justify-content-between">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                    <div class="btn-group">
                        <a href="{{ route('reglements.edit', $reglement->id) }}" class="btn rounded-pill btn-primary">
                            <i class="fas fa-pencil-alt fa-icon-text"></i>Modifier le règlement
                        </a>
                        <form action="{{ route('reglements.destroy', $reglement->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill px-4 ms-2" 
                                    onclick="return confirm('Confirmer la suppression ?')">
                                <i class="fas fa-trash-alt me-2"></i>Supprimer 
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
