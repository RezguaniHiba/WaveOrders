@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Liste des clients</h3>
     @php
        // Détermine le préfixe des routes selon le rôle de l'utilisateur
        $routePrefix = auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.';
        $colspan = auth()->user()->role === 'admin' ? 7 : 6;
    @endphp

    <div class="mb-3 text-end">
       <a href="{{ route($routePrefix . 'create') }}" class="btn btn-success">
        <i class="fa fa-plus"></i> Ajouter un client
    </a>
    </div>

    <table class="table table-hover table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Ville</th>
                @if(auth()->user()->role === 'admin')
                     <th>Commercial</th>
                 @endif
                <th>Date de création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                <tr>
                    <td>{{ $client->nom }}</td>
                    <td>{{ $client->email ?? '-' }}</td>
                    <td>{{ $client->telephone ?? '-' }}</td>
                    <td>{{ $client->ville ?? '-' }}</td>
                     @if(auth()->user()->role === 'admin')
                         <td>{{ $client->utilisateur->nom ?? '-' }}</td>
                     @endif
                    <td>{{ $client->date_creation->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route($routePrefix . 'show', $client) }}" class="btn btn-sm btn-primary" title="Voir">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="{{ route($routePrefix . 'edit', $client) }}" class="btn btn-sm btn-warning" title="Modifier">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route($routePrefix . 'destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce client ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
        <tr>
            <td colspan="{{ $colspan }}">Aucun client trouvé.</td>
        </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $clients->links() }}
    </div>
</div>
@endsection
