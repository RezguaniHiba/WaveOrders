@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="card shadow-lg-custom border-0 mb-4">
        <div class="card-header bg-light-blue text-white py-3">
            <h3 class="h5 mb-0">
                <i class="fas fa-folder-open me-2"></i>Fiche Famille d'Articles
            </h3>
        </div>
        
        <div class="card-body">
            <div class="mb-4">
                <h4 class="fw-bold text-primary mb-3">{{ $famille->libelle }}</h4>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded">
                            <h5 class="h6 fw-bold text-muted mb-3">INFORMATIONS GENERALES</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <span class="fw-medium me-2">Code WaveSoft:</span>
                                    <span class="badge bg-secondary">{{ $famille->code_wavesoft ?? 'Non encore associé' }}</span>
                                </li>
                                <li class="mb-2">
                                    <span class="fw-medium me-2">Famille parente:</span>
                                    @if($famille->parent)
                                        <a href="{{ route('familles-articles.show', $famille->parent) }}" class="text-decoration-none">
                                            {{ $famille->parent->libelle }}
                                        </a>
                                    @else
                                        <span>Aucune (famille racine)</span>
                                    @endif
                                </li>
                                <li>
                                    <span class="fw-medium me-2">Chemin complet:</span>
                                    <span class="text-muted">{{ $famille->cheminComplet() }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded">
                            <h5 class="h6 fw-bold text-muted mb-3">STATISTIQUES</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <span class="fw-medium me-2">Nombre d'articles:</span>
                                    <span class="badge bg-primary">{{ $famille->articles->count() }}</span>
                                </li>
                                <li>
                                    <span class="fw-medium me-2">Sous-familles:</span>
                                    <span class="badge bg-info">{{ $famille->enfants->count() }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            @if ($famille->enfants->count() > 0)
                <div class="mb-4">
                    <h5 class="h6 fw-bold text-muted mb-3 d-flex align-items-center">
                        <i class="fas fa-sitemap me-2"></i>SOUS-FAMILLES
                    </h5>
                    <div class="row g-2">
                        @foreach ($famille->enfants as $enfant)
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="{{ route('familles-articles.show', $enfant) }}" class="text-decoration-none">
                                                <i class="fas fa-folder me-1 text-warning"></i>
                                                {{ $enfant->libelle }}
                                            </a>
                                        </h6>
                                        <div class="d-flex justify-content-between small text-muted">
                                            <span>Articles: {{ $enfant->articles->count() }}</span>
                                            <span>Sous-familles: {{ $enfant->enfants->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        
        <div class="card-footer bg-transparent border-top-0 py-3">
            <div class="d-flex justify-content-between">
                <a href="{{ route('familles-articles.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                </a>
                <div class="btn-group">
                    <a href="{{ route('familles-articles.edit', $famille) }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-edit me-2"></i>Modifier la famille d'Articles
                    </a>
                    @if($famille->articles->count() === 0 && $famille->enfants->count() === 0)
                        <form action="{{ route('familles-articles.destroy', $famille) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill px-4 ms-2" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette famille ?')">
                                <i class="fas fa-trash-alt me-2"></i>Supprimer
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-blue {
        background-color: #1e3a8a;
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
    
    .list-unstyled li {
        padding: 4px 0;
    }
    
    .badge {
        font-weight: 500;
        padding: 4px 8px;
    }
    
    .rounded-pill {
        border-radius: 50px !important;
    }
     .shadow-lg-custom {
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
      .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    
   
</style>
@endsection