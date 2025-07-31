@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Créer un client</h2>
    <form action="{{ route($routePrefix . 'store') }}" method="POST">
        @include('clients.form')
        <button type="submit" class="btn btn-success mt-3">Créer</button>
        <a href="{{ route($routePrefix . 'index') }}" class="btn btn-secondary mt-3">Annuler</a>
    </form>
</div>
@endsection
