@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h3 class="mb-0">
            <i class="fas fa-folder-tree me-2"></i>Liste des familles d'articles
        </h3>
        <div class="d-flex gap-3 flex-wrap">
            <form method="GET" action="{{ route('familles-articles.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control form-select-sm" placeholder="Rechercher..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
            </form>

            <a href="{{ route('familles-articles.create') }}" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                <i class="fas fa-plus"></i> Nouvelle famille
            </a>
        </div>
    </div>

    @if($famillesRacines->count() > 0)
    <div class="card border-0 shadow-lg-custom">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @foreach($famillesRacines as $famille)
                    @include('familles_articles.partials.famille_item', ['famille' => $famille, 'niveau' => 0])
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info text-center py-3">
        <i class="fas fa-info-circle me-2"></i>Aucune famille trouvée.
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
    
    .famille-item {
        transition: background-color 0.2s;
    }
    
    .famille-item:hover {
        background-color: #f8f9fa;
    }
     /* Ombre plus prononcée */
    .shadow-lg-custom {
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
</style>
@endsection