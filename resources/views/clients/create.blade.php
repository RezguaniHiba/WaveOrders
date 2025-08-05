@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-light-blue text-white py-3">
            <h3 class="h5 mb-0">
                <i class="fas fa-user-plus me-2"></i>Créer un nouveau client
            </h3>
        </div>

        <div class="card-body p-4">
            <form action="{{ route($routePrefix . 'store') }}" method="POST">
                @csrf
                @include('clients.form')                    
                <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                    <a href="{{ route($routePrefix . 'index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-light-blue {
        background-color: #1e3a8a;
    }
    
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .form-control, .form-select, .input-group-text {
        border-radius: 6px;
        transition: all 0.2s;
    }
    
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(30, 58, 138, 0.1);
        border-color: #1e3a8a;
    }
    
    .shadow-none {
        box-shadow: none !important;
    }
</style>
@endsection