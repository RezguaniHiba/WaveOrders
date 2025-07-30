@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Détail commande {{ $commande->numero }}</h3>

    <p><strong>Client :</strong> {{ $commande->client->nom }}</p>
    <p><strong>Date commande :</strong> {{ $commande->date_commande->format('d/m/Y') }}</p>
    <p><strong>Statut :</strong> {{ ucfirst($commande->statut) }}</p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Article</th>
                <th>Quantité</th>
                <th>Prix unitaire HT</th>
                <th>Remise %</th>
                <th>Total HT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->lignesCommande as $ligne)
                <tr>
                    <td>{{ $ligne->article->designation ?? 'Inconnu' }}</td>
                    <td>{{ $ligne->quantite }}</td>
                    <td>{{ number_format($ligne->prix_unitaire_ht, 2, ',', ' ') }} €</td>
                    <td>{{ $ligne->remise_percent }}%</td>
                    <td>{{ number_format($ligne->quantite * $ligne->prix_unitaire_ht * (1 - $ligne->remise_percent/100), 2, ',', ' ') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total TTC :</strong> {{ number_format($commande->montant_ttc, 2, ',', ' ') }} €</p>

    <a href="{{ route('commercial.commandes.index') }}" class="btn btn-secondary">Retour</a>
</div>
@endsection 