@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Modifier le client</h2>
    <form action="{{ route((auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.') . 'update', $client->id)
 }}" method="POST">
        @method('PUT')
        @include('clients.form')
        <button type="submit" class="btn btn-primary mt-3">Mettre à jour</button>
        <a href="{{ route((auth()->user()->role === 'admin' ? 'admin.clients.' : 'clients.') . 'show', $client->id) }}" class="btn btn-secondary mt-3">Annuler</a>
    </form>
</div>
@endsection
