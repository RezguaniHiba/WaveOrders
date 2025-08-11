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
    
    .shadow-lg-custom {
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    
    .badge {
        font-size: 0.9rem;
        padding: 0.4em 0.7em;
    }
    
    /* En-tête coloré */
    .header-gradient {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    }
    
    /* Style des cartes d'information */
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
    
    /* Style pour les tableaux */
    .table thead th {
        font-size: 1rem;
    }
    
    .table td, .table th {
        padding: 0.8rem 0.6rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(30, 58, 138, 0.05);
    }
    
    /* Boutons */
    .btn {
        font-size: 1rem;
        padding: 0.5rem 1.5rem;
        border-radius: 0.5rem;
    }
    
    .action-buttons .btn {
        border-radius: 50rem !important;
    }
    
    .fa-icon-text {
        margin-right: 0.6rem;
    }
    
    /* Style spécifique pour la fiche utilisateur */
    .user-info-item {
        margin-bottom: 1rem;
    }
    
    .user-info-label {
        font-weight: 600;
        color: #4b5563;
        min-width: 120px;
        display: inline-block;
    }
    
    .user-info-value {
        color: #1f2937;
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
                <h3 class="mb-0">Fiche Utilisateur #{{ $user->id }}</h3>
            </div>
        </div>
         @php
            $routePrefix = auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.';
        @endphp

        <!-- Corps de la carte -->
        <div class="card-body bg-gray-50">
            <!-- Section Informations utilisateur -->
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
                                <div class="col-md-6">
                                    <div class="user-info-item">
                                        <span class="user-info-label"><i class="fas fa-user fa-icon-text"></i>Nom :</span>
                                        <span class="user-info-value">{{ $user->nom }}</span>
                                    </div>
                                    
                                    <div class="user-info-item">
                                        <span class="user-info-label"><i class="fas fa-envelope fa-icon-text"></i>Email :</span>
                                        <span class="user-info-value">{{ $user->email }}</span>
                                    </div>
                                    
                                    <div class="user-info-item">
                                        <span class="user-info-label"><i class="fas fa-phone fa-icon-text"></i>Téléphone :</span>
                                        <span class="user-info-value">{{ $user->telephone ?? 'Non renseigné' }}</span>
                                    </div>
                                </div>
                                
                                <!-- Colonne Rôle et Statut -->
                                <div class="col-md-6">
                                    <div class="user-info-item">
                                        <span class="user-info-label"><i class="fas fa-user-tag fa-icon-text"></i>Rôle :</span>
                                        <span class="user-info-value badge 
                                            {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </div>
                                    
                                    <div class="user-info-item">
                                        <span class="user-info-label"><i class="fas fa-circle fa-icon-text"></i>Statut :</span>
                                        <span class="user-info-value badge {{ $user->actif ? 'bg-success' : 'bg-warning' }}">
                                            <i class="fas fa-{{ $user->actif ? 'check' : 'times' }} me-1"></i>
                                            {{ $user->actif ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </div>
                                    
                                    <div class="user-info-item">
                                        <span class="user-info-label"><i class="fas fa-calendar-alt fa-icon-text"></i>Créé le :</span>
                                        <span class="user-info-value">{{ $user->date_creation->format('d/m/Y à H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Clients associés -->
            <div class="card info-card mb-6">
                <div class="card-header">
                    <h5 class="mb-0 text-primary-emphasis">
                        <i class="fas fa-users fa-icon-text"></i>Clients Associés
                        <span class="badge bg-primary ms-2">{{ $user->clients->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($user->clients->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="table table-striped table-hover table-bordered border mb-0">
                                <thead class="bg-black-50 text-primary-emphasis">
                                    <tr>
                                        <th class="px-4 py-3">Nom</th>
                                        <th class="px-4 py-3">Email</th>
                                        <th class="px-4 py-3">Téléphone</th>
                                        <th class="px-4 py-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->clients as $client)
                                    <tr class="border-t hover:bg-blue-50">
                                        <td class="px-4 py-3">{{ $client->nom }}</td>
                                        <td class="px-4 py-3">{{ $client->email ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $client->telephone ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="{{ route($routePrefix . 'show', $client) }}" 
                                               class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-eye fa-icon-text"></i>Détail de client
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-user-slash fa-2x mb-6 text-gray-300"></i>
                            <p class="text-lg">Aucun client associé à cet utilisateur</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="mt-4 d-flex justify-content-between action-buttons">
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left fa-icon-text"></i> Retour à la liste
                </a>
                <div>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary me-2">
                        <i class="fas fa-pencil-alt fa-icon-text"></i> Modifier
                    </a>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                            <i class="fas fa-trash fa-icon-text"></i> Supprimer
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection