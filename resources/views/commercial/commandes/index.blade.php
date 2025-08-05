@extends('layouts.app')

@section('content')
<div class="container">
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
{{-- Si des commandes sont disponibles on affiche le table de liste de cmd--}}
    @if($commandes->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Numéro de la commande</th>
                            <th class="text-center">Client</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Total TTC</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Exporté</th>
                             @if(auth()->user()->role === 'admin')
                                    <th class="text-center">Créée par</th>
                             @endif
                            <th class="text-center">Actions</th>
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
                            <td class="text-center fw-bold">{{ $commande->numero }}</td>
                            <td>{{ $commande->client->nom ?? 'Inconnu' }}</td>
                            <td class="text-center">{{ $commande->date_commande->format('d/m/Y') }}</td>
                            <td class="text-center fw-bold text-nowrap">
                                {{ number_format($commande->montant_ttc, 2, ',', ' ') }} DH
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-{{ $couleurs[$commande->statut] ?? 'dark' }}" 
                                    data-bs-toggle="tooltip" title="Statut : {{ ucfirst($commande->statut) }}">
                                    {{ ucfirst($commande->statut) }}
                                </span>
                            </td>
                            <td class="text-center">
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

    {{-- Pagination avec conservation de filtre --}}
    <div class="mt-3 d-flex justify-content-center">
        {{ $commandes->withQueryString()->links() }}
    </div>

    @else
    <div class="alert alert-info text-center py-3">
        <i class="fas fa-info-circle me-2"></i>Aucune commande trouvée pour vos critères.
    </div>
    @endif
</div>
{{-- Activation des tooltips Bootstrap --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Activer tous les tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<style>
    .action-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .table th {
        white-space: nowrap;
    }
    
    .badge {
        min-width: 80px;
    }
</style>
@endsection