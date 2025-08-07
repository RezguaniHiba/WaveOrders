@extends('layouts.app')

@section('title', 'Modifier Règlement - '.$reglement->commande->numero)

@section('content')
<div class="container py-3">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-light-blue text-white py-3">
            <h3 class="h5 mb-0">
                <i class="fas fa-money-bill-wave me-2"></i>
                Modifier règlement - Commande #{{ $reglement->commande->numero }}
            </h3>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('reglements.update', $reglement->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="commande_id" value="{{ $reglement->commande_id }}">
                
                @if($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-4">
                    <!-- Section Commande et Client -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Commande associée
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   value="Commande #{{ $reglement->commande->numero }}" 
                                   readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Client commande
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $reglement->commande->client->nom }}" 
                                   readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type_facturation" class="form-label fw-bold">
                                Type de facturation
                            </label>
                            <select name="type_facturation" id="type_facturation" class="form-select">
                                <option value="">Sélectionner...</option>
                                <option value="facturer_client" {{ $reglement->type_facturation == 'facturer_client' ? 'selected' : '' }}>Facturer client</option>
                                <option value="client_payeur" {{ $reglement->type_facturation == 'client_payeur' ? 'selected' : '' }}>Client payeur</option>
                                <option value="autre" {{ $reglement->type_facturation == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="client_payeur_id" class="form-label fw-bold required">
                                Client payeur
                            </label>
                            <select name="client_payeur_id" id="client_payeur_id" class="form-select" required>
                                <option value="">Même que client commande</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" 
                                        {{ $reglement->client_payeur_id == $client->id ? 'selected' : '' }}>
                                        {{ $client->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Section Paiement -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="montant" class="form-label fw-bold required">
                                Montant (DH)
                            </label>
                            <input type="number" 
                                   step="0.01" 
                                   min="0.01" 
                                   name="montant" 
                                   id="montant" 
                                   value="{{ old('montant', $reglement->montant) }}" 
                                   class="form-control" 
                                   required
                                   max="{{ $reglement->commande->montant_restant + $reglement->montant }}"
                            >
                            <small class="form-text text-muted">
                                Reste à payer: 
                                <span id="reste_a_payer">
                                    {{ number_format($reglement->commande->montant_restant + $reglement->montant, 2) }}
                                </span> DH
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="mode" class="form-label fw-bold required">
                                Mode de paiement
                            </label>
                            <select name="mode" id="mode" class="form-select" required>
                                <option value="">Sélectionner...</option>
                                <option value="especes" {{ $reglement->mode == 'especes' ? 'selected' : '' }}>Espèces</option>
                                <option value="cheque" {{ $reglement->mode == 'cheque' ? 'selected' : '' }}>Chèque</option>
                                <option value="carte_bancaire" {{ $reglement->mode == 'carte_bancaire' ? 'selected' : '' }}>Carte bancaire</option>
                                <option value="virement" {{ $reglement->mode == 'virement' ? 'selected' : '' }}>Virement</option>
                                <option value="autre" {{ $reglement->mode == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_reglement" class="form-label fw-bold">
                                Date du règlement
                            </label>
                            <input type="date" 
                                   name="date_reglement" 
                                   id="date_reglement" 
                                   value="{{ old('date_reglement', $reglement->date_reglement->format('Y-m-d')) }}" 
                                   class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="fichier_justificatif" class="form-label fw-bold">
                                Justificatif de paiement
                            </label>
                            <input type="file" 
                                   name="fichier_justificatif" 
                                   id="fichier_justificatif" 
                                   class="form-control" 
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <small class="form-text text-muted">Format: PDF, JPG, PNG (max: 2MB)</small>
                            @if($reglement->fichier_justificatif)
                                <div class="mt-2">
                                    <a href="{{ Storage::url($reglement->fichier_justificatif) }}" 
                                       target="_blank" 
                                       class="text-primary">
                                        <i class="fas fa-file-pdf me-1"></i> Voir le justificatif actuel
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Commentaire -->
                    <div class="col-12">
                        <label for="commentaire" class="form-label fw-bold">
                            Commentaire
                        </label>
                        <textarea name="commentaire" 
                                  id="commentaire" 
                                  class="form-control" 
                                  rows="3">{{ old('commentaire', $reglement->commentaire) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between pt-4 mt-3 border-top">
                    <div>
                        @can('delete', $reglement)
                        <button type="button" 
                                class="btn btn-outline-danger rounded-pill px-3"
                                onclick="if(confirm('Confirmer la suppression ?')) document.getElementById('delete-form').submit()">
                            <i class="fas fa-trash me-2"></i> Supprimer
                        </button>
                        @endcan
                    </div>
                    
                    <div class="d-flex gap-3">
                        <a href="{{ route('commandes.show', $reglement->commande_id) }}" 
                           class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="fas fa-times me-2"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="fas fa-save me-2"></i> Enregistrer
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Formulaire de suppression caché -->
            @can('delete', $reglement)
            <form id="delete-form" action="{{ route('reglements.destroy', $reglement->id) }}" method="POST" class="d-none">
                @csrf @method('DELETE')
            </form>
            @endcan
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
    
    .required:after {
        content: " *";
        color: #dc3545;
    }
    
    .shadow-none {
        box-shadow: none !important;
    }
    
    a.text-primary:hover {
        text-decoration: underline;
    }
</style>
@endsection