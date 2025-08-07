@extends('layouts.app')

@section('title', isset($commande) ? 'Nouveau Règlement - Commande #'.$commande->numero : 'Nouveau Règlement')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- En-tête dynamique -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-money-bill-wave text-primary me-2"></i>
                    @isset($commande)
                        Nouveau règlement - Commande #{{ $commande->numero }}
                    @else
                        Nouveau règlement
                    @endisset
                </h1>
                <a href="{{ isset($commande) ? route('commandes.show', $commande->id) : route('reglements.index') }}" 
                   class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Retour
                </a>
            </div>

            <!-- Carte du formulaire -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h2 class="h5 mb-0">Informations du règlement</h2>
                </div>
                
                <form action="{{ route('reglements.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @isset($commande)
                        <input type="hidden" name="from_commande" value="1">
                    @endisset
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Section 1 : Commande et clients -->
                        <div class="row mb-4">
                            <!-- Colonne gauche -->
                            <div class="col-md-6">
                                <!-- Sélection de la commande -->
                                @if(!isset($commande))
                                <div class="mb-4">
                                    <label for="commande_id" class="form-label fw-bold required">
                                        Commande associée
                                    </label>
                                    <select name="commande_id" id="commande_id" class="form-select" required>
                                        <option value="">Sélectionner une commande...</option>
                                        @foreach($commandes as $cmd)
                                            <option value="{{ $cmd->id }}" 
                                                {{ old('commande_id') == $cmd->id ? 'selected' : '' }}
                                                data-client-id="{{ $cmd->client_id }}"
                                                data-montant-restant="{{ $cmd->montant_restant }}">
                                                Commande #{{ $cmd->numero }} - {{ $cmd->client->nom }} 
                                                ({{ number_format($cmd->montant_ttc, 2) }} DH)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @else
                                    <input type="hidden" name="commande_id" value="{{ $commande->id }}">
                                @endif

                                <!-- Client commande -->
                                <div class="mb-3">
                                    <label for="client_commande_nom" class="form-label fw-bold">
                                        Client commande
                                    </label>
                                    @if(isset($commande))
                                        <input type="text" 
                                               id="client_commande_nom"
                                               class="form-control" 
                                               value="{{ $commande->client->nom }}" 
                                               readonly>
                                    @else
                                        <input type="text" 
                                               id="client_commande_nom" 
                                               class="form-control" 
                                               readonly>
                                        <input type="hidden" 
                                               id="client_commande_id" 
                                               name="client_commande_id">
                                    @endif
                                </div>
                            </div>

                            <!-- Colonne droite -->
                            <div class="col-md-6">
                                <!-- Type de facturation -->
                                <div class="mb-3">
                                    <label for="type_facturation" class="form-label fw-bold">
                                        Type de facturation
                                    </label>
                                    <select name="type_facturation" id="type_facturation" class="form-select">
                                        <option value="">Sélectionner...</option>
                                        <option value="facturer_client" {{ old('type_facturation') == 'facturer_client' ? 'selected' : '' }}>Facturer client</option>
                                        <option value="client_payeur" {{ old('type_facturation') == 'client_payeur' ? 'selected' : '' }}>Client payeur</option>
                                        <option value="autre" {{ old('type_facturation') == 'autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                </div>

                                <!-- Client payeur -->
                                <div class="mb-3">
                                    <label for="client_payeur_id" class="form-label fw-bold required">
                                        Client payeur
                                    </label>
                                    <select name="client_payeur_id" id="client_payeur_id" class="form-select" required>
                                        @if(isset($commande))
                                            <option value="">Même que client commande</option>
                                            <option value="{{ $commande->client_id }}" selected>
                                                {{ $commande->client->nom }}
                                            </option>
                                        @else
                                            <option value="">Sélectionner un client...</option>
                                        @endif
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" 
                                                {{ old('client_payeur_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2 : Montant et paiement -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <!-- Montant -->
                                <div class="mb-3">
                                    <label for="montant" class="form-label fw-bold required">
                                        Montant (DH)
                                    </label>
                                    <input type="number" 
                                           step="0.01" 
                                           min="0.01" 
                                           name="montant" 
                                           id="montant" 
                                           value="{{ old('montant') }}" 
                                           class="form-control" 
                                           required
                                           @isset($commande)
                                               max="{{ $commande->montant_restant }}"
                                           @endisset
                                    >
                                    <small class="form-text text-muted">
                                        Reste à payer: 
                                        <span id="reste_a_payer">
                                            @isset($commande)
                                                {{ number_format($commande->montant_restant, 2) }}
                                            @else
                                                0.00
                                            @endisset
                                        </span> DH
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <!-- Mode de paiement -->
                                <div class="mb-3">
                                    <label for="mode" class="form-label fw-bold required">
                                        Mode de paiement
                                    </label>
                                    <select name="mode" id="mode" class="form-select" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="especes" {{ old('mode') == 'especes' ? 'selected' : '' }}>Espèces</option>
                                        <option value="cheque" {{ old('mode') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                                        <option value="carte_bancaire" {{ old('mode') == 'carte_bancaire' ? 'selected' : '' }}>Carte bancaire</option>
                                        <option value="virement" {{ old('mode') == 'virement' ? 'selected' : '' }}>Virement</option>
                                        <option value="autre" {{ old('mode') == 'autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <!-- Date règlement -->
                                <div class="mb-3">
                                    <label for="date_reglement" class="form-label fw-bold">
                                        Date du règlement
                                    </label>
                                    <input type="date" 
                                           name="date_reglement" 
                                           id="date_reglement" 
                                           value="{{ old('date_reglement', date('Y-m-d')) }}" 
                                           class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <!-- Fichier justificatif -->
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
                                </div>
                            </div>
                        </div>

                        <!-- Section 3 : Commentaire -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="commentaire" class="form-label fw-bold">
                                    Commentaire
                                </label>
                                <textarea name="commentaire" 
                                          id="commentaire" 
                                          class="form-control" 
                                          rows="3">{{ old('commentaire') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light py-3 d-flex justify-content-end">
                        <input type="hidden" name="cree_par" value="{{ auth()->id() }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Enregistrer le règlement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script pour la gestion dynamique -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const clientsData = @json($clients->map(fn($client) => ['id' => $client->id, 'nom' => $client->nom]));

    const commandeSelect = document.getElementById('commande_id');
    if (commandeSelect) {
        commandeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (!selectedOption) return;

            const clientId = selectedOption.dataset.clientId || '';
            const montantRestant = selectedOption.dataset.montantRestant || '0.00';
            const clientNom = selectedOption.text.split(' - ')[1]?.split(' (')[0] || '';

            // Mettre à jour les champs client commande
            const clientNomInput = document.getElementById('client_commande_nom');
            const clientIdInput = document.getElementById('client_commande_id');
            if(clientNomInput) clientNomInput.value = clientNom;
            if(clientIdInput) clientIdInput.value = clientId;

            // Mettre à jour montant max et reste à payer
            const montantInput = document.getElementById('montant');
            if(montantInput) {
                montantInput.max = montantRestant;
                if (parseFloat(montantInput.value) > parseFloat(montantInput.max)) {
                    montantInput.value = montantInput.max;
                }
            }
            const resteSpan = document.getElementById('reste_a_payer');
            if(resteSpan) resteSpan.textContent = parseFloat(montantRestant).toFixed(2);

            // Mettre à jour le select client payeur
            const clientPayeurSelect = document.getElementById('client_payeur_id');
            if(clientPayeurSelect) {
                let optionsHTML = '<option value="">Même que client commande</option>';
                optionsHTML += `<option value="${clientId}" selected>${clientNom}</option>`;
                clientsData.forEach(client => {
                    if (client.id != clientId) {
                        optionsHTML += `<option value="${client.id}">${client.nom}</option>`;
                    }
                });
                clientPayeurSelect.innerHTML = optionsHTML;
            }
        });

        // Pour déclencher une mise à jour si une commande est déjà sélectionnée (ex : après erreur de validation)
        if (commandeSelect.value) {
            commandeSelect.dispatchEvent(new Event('change'));
        }
    }
});
</script>
@endsection
