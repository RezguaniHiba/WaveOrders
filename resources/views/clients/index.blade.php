@extends('layouts.app')

@section('content')
<div class="container-fluid content-container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h3 class="mb-0">
            <i class="fas fa-users me-2"></i>Liste des clients
        </h3>
        @php
            $routePrefix = auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.';
        @endphp
        <div class="d-flex gap-3 flex-wrap">
            <form method="GET" action="{{ route($routePrefix . 'index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control form-select-sm" placeholder="Rechercher..." value="{{ request('search') }}">
                </div>
                
                @if(auth()->user()->role === 'admin')
                <div class="input-group input-group-sm" style="width: 160px;">
                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                    <select name="commercial_id" class="form-select form-select-sm">
                        <option value="">Tous commerciaux</option>
                       @foreach($commerciaux as $commercial)
                            <option value="{{ $commercial->id }}" @if(request('commercial_id') == (string)$commercial->id) selected @endif>
                                {{ $commercial->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
            </form>

            <a href="{{ route($routePrefix . 'create') }}" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                <i class="fas fa-plus"></i> Nouveau
            </a>
        </div>
    </div>

    @if($clients->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap">Nom complet</th>
                            <th class="text-nowrap">Email</th>
                            <th class="text-nowrap">Téléphone</th>
                            <th class="text-nowrap">Ville</th>
                            @if(auth()->user()->role === 'admin')
                                <th class="text-nowrap">Commercial</th>
                            @endif
                            <th class="text-nowrap" style="width: 90px;">Date de création</th>
                            <th class="text-nowrap text-center">État Financier</th> 
                            <th class="text-center text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                        <tr>
                            <td class="fw-bold text-truncate" style="max-width: 150px;">{{ $client->nom }}</td>
                            <td class="text-truncate" style="max-width: 150px;" title="{{ $client->email ?? '-' }}">{{ $client->email ?? '-' }}</td>
                            <td class="text-nowrap">{{ $client->telephone ?? '-' }}</td>
                            <td class="text-nowrap">{{ $client->ville ?? '-' }}</td>
                            @if(auth()->user()->role === 'admin')
                                <td class="text-truncate" style="max-width: 120px;">{{ $client->utilisateur->nom ?? '-' }}</td>
                            @endif
                            <td class="text-nowrap text-center" style="width: 90px;">{{ $client->date_creation->format('d/m/y') }}</td>
                             <td class="text-center">
                                <a href="{{ route('clients.situation', $client) }}" 
                                class="btn btn-sm btn-outline-info py-1 px-2"
                                title="Télécharger la situation complète"
                                data-bs-toggle="tooltip"
                                target="_blank">
                                    <i class="fas fa-file-invoice-dollar me-1"></i>
                                    <span class="d-none d-md-inline">Situation</span>
                                </a>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route($routePrefix . 'show', $client) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-circle action-btn" 
                                       title="Voir"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route($routePrefix . 'edit', $client) }}" 
                                       class="btn btn-sm btn-outline-warning rounded-circle action-btn" 
                                       title="Modifier"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route($routePrefix . 'destroy', $client) }}" method="POST" style="display:inline;">
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
        {{ $clients->withQueryString()->links() }}
    </div>

    @else
    <div class="alert alert-info text-center py-2">
        <i class="fas fa-info-circle me-1"></i>Aucun client trouvé
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
         .btn-situation span {
            display: none;
        }
        .btn-situation i {
            margin-right: 0 !important;
        }
    }

    .table {
        font-size: 0.85rem;
    }
    .table td {
    vertical-align: middle !important;
}

    /* Bouton situation */
    .btn-situation {
        min-width: 90px;
    }
    .action-btn {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    
    .action-btn i {
        font-size: 0.75rem;
    }
    
    .table th, .table td {
        padding: 0.5rem;
        vertical-align: middle;
    }
    
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

</style>
@endsection