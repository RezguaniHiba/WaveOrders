@extends('layouts.app')

@section('title', isset($commande) ? "Règlements - Commande #{$commande->numero}" : 'Liste des règlements')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">
            @if(isset($commande))
                <i class="fas fa-money-bill-wave mr-2"></i>Règlements - Commande #{{ $commande->numero }}
                <a href="{{ route('reglements.index') }}" class="text-sm text-blue-600 ml-4">
                    <i class="fas fa-list"></i> Voir tous les règlements
                </a>
            @else
                <i class="fas fa-money-bill-wave mr-2"></i>Liste des règlements
                <a href="{{ route('reglements.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nouveau règlement
                </a>
            @endif
        </h1>
        
        @if(isset($commande))
        <a href="{{ route('commandes.reglements.create', $commande->id) }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Nouveau règlement
            </a>
        @endif
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @if(!isset($commande))
                        <th class="px-4 py-3 text-left">Commande</th>
                    @endif
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Montant</th>
                    <th class="px-4 py-3 text-left">Mode</th>
                    <th class="px-4 py-3 text-left">Facturation</th>
                    <th class="px-4 py-3 text-left">Saisi par</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($reglements as $reglement)
                <tr class="hover:bg-gray-50">
                    @if(!isset($commande))
                        <td class="px-4 py-4">
                            <a href="{{ route('commandes.show', $reglement->commande_id) }}" class="text-blue-600 hover:underline">
                                #{{ $reglement->commande->numero }}
                            </a>
                        </td>
                    @endif
                    <td class="px-4 py-4">{{ $reglement->date_reglement->format('d/m/Y') }}</td>
                    <td class="px-4 py-4 font-medium">{{ number_format($reglement->montant, 2, ',', ' ') }} DH</td>
                    <td class="px-4 py-4">
                        <span class="badge bg-{{ [
                            'especes' => 'blue-100 text-blue-800',
                            'cheque' => 'green-100 text-green-800',
                            'carte_bancaire' => 'purple-100 text-purple-800',
                            'virement' => 'indigo-100 text-indigo-800'
                        ][$reglement->mode] ?? 'gray-100 text-gray-800' }}">
                            {{ $reglement->mode_libelle }}
                        </span>
                    </td>
                    <td class="px-4 py-4">{{ $reglement->type_facturation_libelle }}</td>
                    <td class="px-4 py-4">{{ $reglement->utilisateur->nom }}</td>
                    <td class="px-4 py-4 text-center space-x-2">
                        <a href="{{ route('reglements.show', $reglement->id) }}" class="text-blue-600 hover:text-blue-800" title="Voir">
                            <i class="fas fa-eye"></i>
                        </a>
                        @can('update', $reglement)
                        <a href="{{ route('reglements.edit', $reglement->id) }}" class="text-yellow-600 hover:text-yellow-800" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endcan
                        @can('delete', $reglement)
                        <form action="{{ route('reglements.destroy', $reglement->id) }}" method="POST" class="inline" onsubmit="return confirm('Confirmer la suppression ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ isset($commande) ? '7' : '8' }}" class="px-4 py-6 text-center text-gray-500">
                        <i class="fas fa-info-circle mr-2"></i>Aucun règlement trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $reglements->links() }}
    </div>
</div>
@endsection