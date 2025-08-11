@extends('layouts.app')

@section('content')
<div class="container-fluid content-container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h3 class="mb-0">
            <i class="fas fa-boxes me-2"></i>Liste des commandes
        </h3>
        <div class="d-flex gap-3 flex-wrap">
            <form method="GET" action="{{ route('commandes.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group input-group-sm" style="width: 180px;">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <select name="client_id" class="form-select form-select-sm">
                        <option value="">Tous les clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" @if(request('client_id') == $client->id) selected @endif>
                                {{ $client->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 180px;">
                    <span class="input-group-text"><i class="fas fa-filter"></i></span>
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous les statuts</option>
                        @foreach($statuts as $statut => $color)
                                <option value="{{ $statut }}" @if(request('statut') == $statut) selected @endif>
                                    {{ ucfirst(str_replace('_', ' ', $statut)) }}
                                </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
            </form>

            <a href="{{ route('commandes.create') }}" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                <i class="fas fa-plus"></i> Nouvelle commande
            </a>
        </div>
    </div>

    @if($commandes->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-nowrap">N° Commande</th>
                            <th class="text-center text-nowrap">Client</th>
                            <th class="text-center text-nowrap">Date</th>
                            <th class="text-center text-nowrap">Total TTC</th>
                            <th class="text-center text-nowrap">Paiement</th>
                            <th class="text-center text-nowrap">Statut</th>
                            <th class="text-center text-nowrap">Exporté</th>
                            <th class="text-center text-nowrap">Règlements</th>
                            @if(auth()->user()->role === 'admin')
                                <th class="text-center text-nowrap">Créée par</th>
                            @endif
                            <th class="text-center text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $couleurs = [
                                'brouillon' => 'secondary',
                                'consignation' => 'warning',
                                'reserve' => 'info',
                                'partiellement_livree' => 'primary',
                                'complètement_livree' => 'success',
                                'annulee' => 'danger'
                            ];
                        @endphp

                        @foreach ($commandes as $commande)
                        <tr>
                            <td class="text-center fw-bold text-nowrap">{{ $commande->numero }}</td>
                            <td class="text-truncate" style="max-width: 150px;">{{ $commande->client->nom ?? 'Inconnu' }}</td>
                            <td class="text-center text-nowrap">{{ $commande->date_commande->format('d/m/Y') }}</td>
                            <td class="text-center fw-bold text-nowrap">
                                {{ number_format($commande->montant_ttc, 2, ',', ' ') }} DH
                            </td>
                            <td class="text-center text-nowrap">
                                @php
                                    $totalRegle = $commande->reglements->sum('montant');
                                    $etatPaiement = 'Non payé';
                                    $couleurPaiement = 'danger';

                                    if ($totalRegle >= $commande->montant_ttc) {
                                        $etatPaiement = 'Complet';
                                        $couleurPaiement = 'success';
                                    } elseif ($totalRegle > 0) {
                                        $etatPaiement = 'Partiel';
                                        $couleurPaiement = 'warning';
                                    }
                                @endphp
                                <span class="badge rounded-pill bg-{{ $couleurPaiement }}" data-bs-toggle="tooltip" title="{{ $etatPaiement }}">
                                    {{ $etatPaiement }}
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <span class="badge rounded-pill bg-{{ $couleurs[$commande->statut] ?? 'dark' }}" 
                                    data-bs-toggle="tooltip" title="{{ ucfirst($commande->statut) }}">
                                    {{ ucfirst($commande->statut) }}
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                @if($commande->wavesoft_piece_id)
                                    <span class="text-success" data-bs-toggle="tooltip" title="Commande exportée">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                @else
                                    <span class="text-muted" data-bs-toggle="tooltip" title="Non exportée">
                                        <i class="fas fa-times-circle"></i>
                                    </span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="{{ route('commandes.reglements.index', $commande->id) }}"
                                    class="btn btn-sm btn-link text-decoration-none"
                                    title="Voir les règlements">
                                    {{ $commande->reglements->count() }} règlement(s)
                                </a>
                            </td>
                            @if(auth()->user()->role === 'admin')
                            <td class="text-center">
                                {{ $commande->utilisateur->nom ?? 'Non spécifié' }}
                            </td>
                            @endif

                            <td class="text-center text-nowrap">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('commandes.show', $commande->id) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-circle action-btn" 
                                       title="Voir"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>                                   
                                    <a href="{{ route('commandes.edit', $commande->id) }}" 
                                       class="btn btn-sm btn-outline-warning rounded-circle action-btn" 
                                       title="Modifier"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    <form action="{{ route('commandes.destroy', $commande->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger rounded-circle action-btn" 
                                                title="Annuler" 
                                                onclick="return confirm('Confirmer l\'annulation ?')"
                                                data-bs-toggle="tooltip">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $commandes->withQueryString()->links() }}
    </div>

    @else
    <div class="alert alert-info text-center py-3">
        <i class="fas fa-info-circle me-2"></i>Aucune commande trouvée pour vos critères.
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<style>
     .content-container {
        padding-left: var(--content-padding-x);
        padding-right: var(--content-padding-x);
    }

    @media (max-width: 768px) {
        .content-container { 
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }

    .table {
        font-size: 0.875rem;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
   
    .action-btn i {
        font-size: 0.75rem;
    }
    
    .table th {
        white-space: nowrap;
    }
    
    .badge {
        min-width: 80px;
    }

    .text-truncate {
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection