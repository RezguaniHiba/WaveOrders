@extends('layouts.app')

@section('title', 'Fiche client')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-4">
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold text-gray-800">Fiche client</h2>
            <div class="space-x-2">
                <a href="{{ route((auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.') . 'edit', $client->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Modifier</a>
                <form action="{{ route((auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.') . 'destroy', $client->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Confirmer la suppression ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Supprimer</button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p><span class="font-semibold">Nom :</span> {{ $client->nom }}</p>
                <p><span class="font-semibold">Email :</span> {{ $client->email ?? 'N/A' }}</p>
                <p><span class="font-semibold">Téléphone :</span> {{ $client->telephone ?? 'N/A' }}</p>
            </div>
            <div>
                <p><span class="font-semibold">Adresse :</span> {{ $client->adresse ?? 'N/A' }}</p>
                <p><span class="font-semibold">Ville :</span> {{ $client->ville ?? 'N/A' }} - {{ $client->code_postal }}</p>
                <p><span class="font-semibold">Pays :</span> {{ $client->pays }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Commandes liées</h3>

        @if ($client->commandes->count() > 0)
            <table class="w-full text-left table-auto border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">#</th>
                        <th class="px-4 py-2">Numéro</th>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Montant TTC</th>
                        <th class="px-4 py-2">Statut</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($client->commandes as $commande)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2">{{ $commande->numero }}</td>
                            <td class="px-4 py-2">{{ $commande->date_commande->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ number_format($commande->montant_ttc, 2, ',', ' ') }} €</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-sm rounded-full bg-gray-200">{{ $commande->statut }}</span>
                            </td>
                            <td class="px-4 py-2">
                                <a href="{{ route((auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.') . 'show', $commande->id) }}" class="text-blue-600 hover:underline">Voir</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-gray-500">Aucune commande pour ce client.</p>
        @endif
    </div>
</div>
@endsection