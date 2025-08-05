@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h3 class="mb-0">
            <i class="fas fa-users me-2"></i>Liste des clients
        </h3>
         @php
            // Détermine le préfixe des routes selon le rôle de l'utilisateur
            $routePrefix = auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.';
            $colspan = auth()->user()->role === 'admin' ? 7 : 6;
        @endphp
        <div class="d-flex gap-3 flex-wrap">
            <form method="GET" action="{{ route($routePrefix . 'index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group input-group-sm" style="width: 180px;">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control form-select-sm" placeholder="Rechercher..." value="{{ request('search') }}">
                </div>
                
                @if(auth()->user()->role === 'admin')
                <div class="input-group input-group-sm" style="width: 180px;">
                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                    <select name="commercial_id" class="form-select form-select-sm">
                        <option value="">Tous les commerciaux</option>
                       @foreach($commerciaux as $commercial)
                            <option value="{{ $commercial->id }}"  @if(request('commercial_id') == (string)$commercial->id) selected @endif>
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
                <i class="fas fa-plus"></i> Nouveau client
            </a>
        </div>
    </div>

    @if($clients->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Ville</th>
                            @if(auth()->user()->role === 'admin')
                                <th>Commercial</th>
                            @endif
                            <th>Date de création</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                        <tr>
                            <td class="fw-bold">{{ $client->nom }}</td>
                            <td>{{ $client->email ?? '-' }}</td>
                            <td>{{ $client->telephone ?? '-' }}</td>
                            <td>{{ $client->ville ?? '-' }}</td>
                            @if(auth()->user()->role === 'admin')
                                <td>{{ $client->utilisateur->nom ?? '-' }}</td>
                            @endif
                            <td>{{ $client->date_creation->format('d/m/Y') }}</td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex justify-content-center gap-2">
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

    {{-- Pagination avec conservation des filtres --}}
    <div class="mt-3 d-flex justify-content-center">
        {{ $clients->withQueryString()->links() }}
    </div>

    @else
    <div class="alert alert-info text-center py-3">
        <i class="fas fa-info-circle me-2"></i>Aucun client trouvé pour vos critères.
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
</style>
@endsection