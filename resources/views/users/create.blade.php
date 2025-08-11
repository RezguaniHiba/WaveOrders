@extends('layouts.app')

@section('content')
<div class="container py-2">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-light-blue text-white py-3">
            <h3 class="h5 mb-0">
                <i class="fas fa-user me-2"></i>{{ isset($user) ? 'Éditer' : 'Créer' }} un utilisateur
            </h3>
        </div>

        <div class="card-body p-4">
            <form action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" method="POST">
                @csrf
                @if(isset($user))
                    @method('PUT')
                @endif

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="nom" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" id="nom" name="nom" class="form-control shadow-none" 
                               value="{{ old('nom', $user->nom ?? '') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email" class="form-control shadow-none" 
                               value="{{ old('email', $user->email ?? '') }}" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="telephone" class="form-label fw-semibold">Téléphone</label>
                        <input type="text" id="telephone" name="telephone" class="form-control shadow-none" 
                               value="{{ old('telephone', $user->telephone ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label for="role" class="form-label fw-semibold">Rôle <span class="text-danger">*</span></label>
                        <select id="role" name="role" class="form-select shadow-none" required>
                            <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="commercial" {{ old('role', $user->role ?? '') === 'commercial' ? 'selected' : '' }}>Commercial</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                   <div class="col-md-6">
                        <label for="mot_de_passe" class="form-label fw-semibold">
                            Mot de passe @if(!isset($user))<span class="text-danger">*</span>@endif
                        </label>
                        <input type="password" id="mot_de_passe" name="mot_de_passe" 
                               class="form-control shadow-none" {{ isset($user) ? '' : 'required' }}>
                        @if(isset($user))
                            <small class="text-muted">Laisser vide pour ne pas changer</small>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label for="mot_de_passe_confirmation" class="form-label fw-semibold">
                            Confirmation mot de passe @if(!isset($user))<span class="text-danger">*</span>@endif
                        </label>
                        <input type="password" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation" 
                            class="form-control shadow-none" {{ isset($user) ? '' : 'required' }}>
                    </div>
                </div>

                @if(isset($user))
                <div class="form-check form-switch mb-4">
                    <input type="checkbox" id="actif" name="actif" class="form-check-input" 
                           value="1" {{ old('actif', $user->actif) ? 'checked' : '' }}>
                    <label for="actif" class="form-check-label fw-semibold">Compte actif</label>
                </div>
                @endif

                <div class="d-flex justify-content-end gap-3 pt-3">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
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
    
    .rounded-pill {
        padding: 0.375rem 1.25rem;
    }
    
    .form-check-input {
        width: 2.5em;
        height: 1.5em;
    }
    
    .form-switch .form-check-input {
        cursor: pointer;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c2c7;
        color: #842029;
    }
    
    @media (max-width: 768px) {
        .rounded-pill {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection