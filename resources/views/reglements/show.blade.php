@extends('layouts.app')

@section('title', 'Détail Règlement - #'.$reglement->id)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">
            <i class="fas fa-money-bill-wave mr-2"></i>Détail du règlement #{{ $reglement->id }}
        </h1>
        
        <div class="flex space-x-2">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-2"></i> Retour
            </a>
            <a href="{{ route('reglements.edit', $reglement->id) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-2"></i> Modifier
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- En-tête -->
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
            <div>
                <h2 class="text-lg font-medium text-gray-900">
                    Règlement pour la commande #{{ $reglement->commande->numero }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Créé le {{ $reglement->created_at->format('d/m/Y à H:i') }}
                    par {{ $reglement->utilisateur->nom }}
                </p>
            </div>
            <div class="flex items-center">
                <span class="px-3 py-1 rounded-full text-sm font-medium 
                    @if($reglement->commande->statut === 'payee')
                        bg-green-100 text-green-800
                    @else
                        bg-blue-100 text-blue-800
                    @endif">
                    {{ $reglement->commande->statut === 'payee' ? 'Commande payée' : 'Commande en cours' }}
                </span>
            </div>
        </div>

        <!-- Corps -->
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Colonne gauche -->
                <div>
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Date du règlement</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $reglement->date_reglement->format('d/m/Y') }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Montant</label>
                                <p class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ number_format($reglement->montant, 2, ',', ' ') }} DH
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Mode de paiement</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    <span class="px-2 py-1 rounded 
                                        @if($reglement->mode === 'especes') bg-blue-100 text-blue-800 @endif
                                        @if($reglement->mode === 'cheque') bg-green-100 text-green-800 @endif
                                        @if($reglement->mode === 'carte_bancaire') bg-purple-100 text-purple-800 @endif
                                        @if($reglement->mode === 'virement') bg-indigo-100 text-indigo-800 @endif
                                        @if($reglement->mode === 'autre') bg-gray-100 text-gray-800 @endif">
                                        {{ $reglement->mode_libelle }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Clients</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Client commande</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $reglement->commande->client->nom }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Client payeur</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $reglement->clientPayeur->nom }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Type de facturation</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $reglement->type_facturation_libelle }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Colonne droite -->
                <div>
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Justificatif</h3>
                        
                        @if($reglement->fichier_justificatif)
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <div class="flex items-center">
                                    <i class="fas fa-file-pdf text-red-500 text-2xl mr-3"></i>
                                    <div>
                                        <p class="font-medium">Document justificatif</p>
                                        <p class="text-sm text-gray-500">
                                            {{ basename($reglement->fichier_justificatif) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ Storage::url($reglement->fichier_justificatif) }}" 
                                       target="_blank" 
                                       class="btn btn-outline-primary mr-2">
                                        <i class="fas fa-eye mr-2"></i> Voir le document
                                    </a>
                                    <a href="{{ Storage::url($reglement->fichier_justificatif) }}" 
                                       download 
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-download mr-2"></i> Télécharger
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="border rounded-lg p-4 bg-gray-50 text-center text-gray-500">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                Aucun justificatif associé à ce règlement
                            </div>
                        @endif
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Commentaire</h3>
                        
                        @if($reglement->commentaire)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-gray-800 whitespace-pre-line">{{ $reglement->commentaire }}</p>
                            </div>
                        @else
                            <div class="bg-gray-50 p-4 rounded-lg text-center text-gray-500">
                                <i class="fas fa-info-circle mr-2"></i>
                                Aucun commentaire pour ce règlement
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="px-6 py-4 bg-gray-50 border-t flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500">
                    Dernière modification: {{ $reglement->updated_at->format('d/m/Y à H:i') }}
                </p>
            </div>
            <div>
                <form action="{{ route('reglements.destroy', $reglement->id) }}" method="POST" class="inline" onsubmit="return confirm('Confirmer la suppression ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-trash mr-2"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection