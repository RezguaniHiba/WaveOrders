@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>

    /* Style des cartes amélioré */
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
    
    /* Taille de police ajustée */
    body {
        font-size: 0.95rem; /* Légère réduction */
    }
    
    .card-body {
        font-size: 1rem; /* Réduction à 1rem */
        line-height: 1.5; /* Légère réduction */
    }
    
    .card-header h5 {
        font-size: 1.3rem; /* Réduction de 1.5rem à 1.3rem */
    }
    
    .card-body h6 {
        font-size: 1.05rem; /* Réduction de 1.15rem à 1.05rem */
        margin-bottom: 0.5rem;
    }
    
    .card-body p, .card-body td {
        font-size: 1rem; /* Réduction à 1rem */
    }

    /* Espacement entre icônes et texte */
    .fa-icon-text {
        margin-right: 0.6rem; /* Légère réduction */
    }
    
    /* Ajustement des badges */
    .badge {
        font-size: 0.9rem; /* Légère réduction */
        padding: 0.4em 0.7em;
    }

    /* Effet de survol pour les lignes du tableau */
    .table-hover tbody tr:hover {
        background-color: rgba(30, 58, 138, 0.05);
    }
    
    /* Ombre plus prononcée pour la carte principale */
    .shadow-lg-custom {
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    
    /* Style des boutons ajusté */
    .btn {
        font-size: 1rem; /* Réduction à 1rem */
        padding: 0.5rem 1.5rem;
    }
    
    /* Meilleur espacement général */
    .mb-3 {
        margin-bottom: 1rem !important; /* Légère réduction */
    }
    
    /* Ajustement du tableau */
    .table thead th {
        font-size: 1rem; /* Réduction à 1rem */
    }
    
    .table td, .table th {
        padding: 0.8rem 0.6rem; /* Légère réduction */
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="card border-0 shadow-lg-custom">
        <!-- En-tête bleu foncé -->
        <div class="card-header text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Détail de la commande <strong>#{{ $commande->numero }}</strong></h3>
                <span class="badge bg-light text-dark">{{ ucfirst($commande->statut) }}</span>
            </div>
        </div>

        <div class="card-body bg-gray-50">
            <!-- Cartes Informations -->
            <div class="row g-4 mb-4">
                <!-- Carte Client -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-primary-emphasis">
                                <i class="fas fa-user-circle fa-icon-text"></i>Informations client
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-muted"><i class="fas fa-user fa-icon-text"></i>Nom</h6>
                                <p class="mb-0 font-weight-bold">{{ $commande->client->nom }}</p>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted"><i class="fas fa-envelope fa-icon-text"></i>Email</h6>
                                    <p class="mb-0">{{ $commande->client->email ?? 'Non renseigné' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted"><i class="fas fa-phone fa-icon-text"></i>Téléphone</h6>
                                    <p class="mb-0">{{ $commande->client->telephone ?? 'Non renseigné' }}</p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-muted"><i class="fas fa-map-marker-alt fa-icon-text"></i>Adresse</h6>
                              <p class="mb-0">
                                {{ $commande->client->adresse ?? '—' }}
                                @if($commande->client->ville) , {{ $commande->client->ville }}@endif
                                @if($commande->client->code_postal) {{ $commande->client->code_postal }}@endif
                                @if($commande->client->pays), {{ $commande->client->pays }}@endif
                            </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte Commande -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-primary-emphasis">
                                <i class="fas fa-file-invoice fa-icon-text"></i>Détails de la commande
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted"><i class="far fa-calendar-alt fa-icon-text"></i>Date de la commande</h6>
                                    <p class="mb-0">{{ $commande->date_commande->format('d/m/Y') }}</p>
                                </div>
                                @if($commande->date_livraison_prevue)
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted"><i class="fas fa-truck fa-icon-text"></i>Date de livraison prévue</h6>
                                    <p class="mb-0">{{ \Carbon\Carbon::parse($commande->date_livraison_prevue)->format('d/m/Y') }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <h6 class="text-muted"><i class="fas fa-info-circle fa-icon-text"></i>État de la commande</h6>
                                <p class="mb-0 text-light">
                                    <span class="badge bg-{{ $statuts[$commande->statut] ?? 'dark' }}">
                                        {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                                    </span>
                                </p>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-muted"><i class="fas fa-exchange-alt fa-icon-text"></i>Synchronisé avec WaveSoft</h6>
                                <p class="mb-0">
                                    @if($commande->wavesoft_piece_id)
                                        <span class="badge badge-success text-dark"><i class="fas fa-check me-2"></i>Exportée (ID: {{ $commande->wavesoft_piece_id }})</span>
                                    @else
                                        <span class="badge badge-secondary text-dark"><i class="fas fa-times me-2"></i>Non exportée</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Articles -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 text-primary-emphasis">
                        <i class="fas fa-boxes fa-icon-text"></i>Articles commandés
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered border mb-0">
                            <thead class="bg-black-50 text-primary-emphasis">
                                <tr>
                                    <th class="ps-4">Article</th>
                                    <th>Référence</th>
                                    <th class="text-center">Quantité</th>
                                    <th class="text-center">Prix Unitaire HT</th>
                                    <th class="text-center">Remise</th>
                                    <th class="text-center">Taux de TVA</th>
                                    <th class="text-center">Total Hors Taxes</th>
                                    <th class="text-center">Total TTC</th>
                                    <th class="pe-4 text-center">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($commande->lignesCommande as $ligne)
                                    <tr>
                                        <td class="ps-4">{{ $ligne->article->designation ?? 'Inconnu' }}</td>
                                        <td>{{ $ligne->article->reference ?? '-' }}</td>
                                        <td class="text-center">{{ $ligne->quantite }}</td>
                                        <td class="text-center">{{ number_format($ligne->prix_unitaire_ht, 2, ',', ' ') }} DH</td>
                                        <td class="text-center">{{ $ligne->remise_percent }} %</td>
                                        <td class="text-center">{{ $ligne->taux_tva }}%</td>
                                        <td class="text-center">{{ number_format($ligne->montant_ht, 2, ',', ' ') }} DH</td>
                                        <td class="text-center">{{ number_format($ligne->montant_ht + $ligne->montant_tva, 2, ',', ' ') }} DH</td>
                                        <td class="pe-4 text-center">
                                            <span class="text-light badge bg-{{ [
                                                'en_attente' => 'secondary',
                                                'reserve' => 'info',
                                                'en_consigne' => 'warning',
                                                'prepare' => 'primary',
                                                'livre' => 'success',
                                                'annule' => 'danger'
                                            ][$ligne->statut] ?? 'dark' }}">
                                                {{ ucfirst(str_replace('_', ' ', $ligne->statut)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                            Aucun article dans cette commande.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Récapitulatif et notes -->
            <div class="row g-4">
                <!-- Notes -->
                @if($commande->notes)
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-primary-emphasis">
                                <i class="fas fa-sticky-note fa-icon-text"></i>Notes
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="p-3 bg-light rounded">
                                <p class="mb-0">{{ $commande->notes }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Récapitulatif -->
                <div class="@if($commande->notes) col-lg-5 @else col-12 @endif">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-primary-emphasis">
                                <i class="fas fa-calculator fa-icon-text"></i>Récapitulatif
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="p-3">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Sous-total HT :</span>
                                    <strong>{{ number_format($commande->montant_ht, 2, ',', ' ') }} DH</strong>
                                </div>
                                @if($commande->remise_percent > 0)
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="text-muted">Remise ({{ $commande->remise_percent }}%) :</span>
                                        <strong class="text-danger">-{{ number_format($commande->montant_ht * $commande->remise_percent / 100, 2, ',', ' ') }} DH</strong>
                                    </div>
                                @elseif($commande->remise_montant > 0)
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="text-muted">Remise :</span>
                                        <strong class="text-danger">-{{ number_format($commande->remise_montant, 2, ',', ' ') }} DH</strong>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">TVA :</span>
                                    <strong>{{ number_format($commande->montant_tva, 2, ',', ' ') }} DH</strong>
                                </div>
                                <hr class="my-3">
                                <div class="d-flex justify-content-between font-weight-bold h5 text-body-emphasis">
                                    <span>Total TTC :</span>
                                    <strong>{{ number_format($commande->montant_ttc, 2, ',', ' ') }} DH</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-arrow-left fa-icon-text"></i> Retour à la liste
                </a>
                
                <div class="btn-group">
                    <button class="btn btn-outline-primary rounded-pill px-4 me-2">
                        <i class="fas fa-print fa-icon-text"></i> Imprimer
                    </button>
                    <button class="btn btn-primary rounded-pill px-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); border: none;">
                        <i class="fas fa-pencil-alt fa-icon-text"></i> Modifier
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection