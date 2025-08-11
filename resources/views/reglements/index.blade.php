@extends('layouts.app')

@section('title', isset($commande) ? "Règlements - Commande #{$commande->numero}" : 'Liste des règlements')

@section('content')
<div class="container-fluid content-container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h3 class="mb-0">
            <i class="fas fa-money-bill-wave me-2"></i>
            @if(isset($commande))
                Règlements - Commande #{{ $commande->numero }}
                <a href="{{ route('reglements.index') }}" class="btn btn-sm btn-outline-secondary ms-3">
                    <i class="fas fa-list me-1"></i> Voir tous
                </a>
            @else
                Liste des règlements
            @endif
        </h3>
        
        <div class="d-flex gap-3 flex-wrap">
            @if(isset($commande))
                <a href="{{ route('commandes.reglements.create', $commande->id) }}" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                    <i class="fas fa-plus me-1"></i> Nouveau règlement
                </a>
            @else
                <a href="{{ route('reglements.create') }}" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                    <i class="fas fa-plus me-1"></i> Nouveau règlement
                </a>
            @endif
        </div>
    </div>

    @if($reglements->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            @if(!isset($commande))
                                <th class="text-center text-nowrap">Commande</th>
                            @endif
                            <th class="text-center text-nowrap">Date</th>
                            <th class="text-center text-nowrap">Montant</th>
                            <th class="text-center text-nowrap">Mode</th>
                            <th class="text-center text-nowrap">Type facturation</th>
                            <th class="text-center text-nowrap">Saisi par</th>
                            <th class="text-center text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reglements as $reglement)
                        <tr>
                            @if(!isset($commande))
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('commandes.show', $reglement->commande_id) }}" class="text-primary text-decoration-none">
                                        #{{ $reglement->commande->numero }}
                                    </a>
                                </td>
                            @endif
                            <td class="text-center text-nowrap">{{ $reglement->date_reglement->format('d/m/Y') }}</td>
                            <td class="fw-bold text-center text-nowrap">{{ number_format($reglement->montant, 2, ',', ' ') }} DH</td>
                            <td class="text-center text-nowrap">
                                <span class="badge rounded-pill bg-{{ [
                                    'especes' => 'primary',
                                    'cheque' => 'success',
                                    'carte_bancaire' => 'info',
                                    'virement' => 'secondary'
                                ][$reglement->mode] ?? 'light text-dark' }}">
                                    {{ $reglement->mode_libelle }}
                                </span>
                            </td>
                            <td class="text-center text-nowrap">{{ $reglement->type_facturation_libelle }}</td>
                            <td class="text-center text-nowrap">{{ $reglement->utilisateur->nom }}</td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('reglements.show', $reglement->id) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-circle action-btn" 
                                       title="Voir"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('reglements.edit', $reglement->id) }}" 
                                       class="btn btn-sm btn-outline-warning rounded-circle action-btn" 
                                       title="Modifier"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('reglements.destroy', $reglement->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger rounded-circle action-btn" 
                                                title="Supprimer" 
                                                onclick="return confirm('Confirmer la suppression ?')"
                                                data-bs-toggle="tooltip">
                                            <i class="fas fa-trash"></i>
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
        {{ $reglements->withQueryString()->links() }}
    </div>

    @else
    <div class="alert alert-info text-center py-3">
        <i class="fas fa-info-circle me-2"></i>Aucun règlement trouvé
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
        min-width: 70px;
        font-weight: 500;
        padding: 4px 8px;
    }

    .text-truncate {
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection