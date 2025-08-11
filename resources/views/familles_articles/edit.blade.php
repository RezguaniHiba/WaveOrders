@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-light-blue text-white py-3">
            <h3 class="h5 mb-0">
                <i class="fas fa-edit me-2"></i>Modifier la famille : {{ $famille->libelle }}
            </h3>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('familles-articles.update', $famille->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="libelle" class="form-label fw-medium">Libellé <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-tag text-primary"></i></span>
                        <input type="text" name="libelle" class="form-control" 
                               value="{{ old('libelle', $famille->libelle) }}" required
                               placeholder="Nom de la famille">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="parent_id" class="form-label fw-medium">Famille parente</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-folder-tree text-primary"></i></span>
                        <select name="parent_id" class="form-select">
                            <option value="">-- Aucune (famille racine) --</option>
                            {{--Empêcher de choisir comme famille parente :Elle-même ou un de ses propres descendants cela va creer une boucle récursive, ce qui casserait la hiérarchie--}}
                            @foreach ($familles as $f)
                                @if ($f->id !== $famille->id && !$f->getAllDescendantIds()->contains($famille->id))
                                    <option value="{{ $f->id }}" {{ old('parent_id', $famille->parent_id) == $f->id ? 'selected' : '' }}>
                                        {{ $f->cheminComplet() }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <small class="text-muted mt-1 d-block">Ne peut pas être elle-même ou un de ses descendants</small>
                </div>

                <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                    <a href="{{ route('familles-articles.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="fas fa-save me-2"></i>Enregistrer les modifications
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
    
    .input-group-text {
        background-color: #f8f9fa;
    }
    
    .alert-danger {
        border-left: 4px solid #dc3545;
    }
</style>
@endsection