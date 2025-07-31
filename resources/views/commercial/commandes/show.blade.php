@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Détail de la commande <strong>#{{ $commande->numero }}</strong></h3>

    <div class="mb-4">
        <p><strong>Client :</strong> {{ $commande->client->nom }}</p>
        <p><strong>Email :</strong> {{ $commande->client->email ?? 'Non renseigné' }}</p>
        <p><strong>Téléphone :</strong> {{ $commande->client->telephone ?? 'Non renseigné' }}</p>
        <p><strong>Adresse :</strong> {{ $commande->client->adresse ?? '—' }} {{ $commande->client->ville }} {{ $commande->client->code_postal }}, {{ $commande->client->pays }}</p>

        <p><strong>Date commande :</strong> {{ $commande->date_commande->format('d/m/Y') }}</p>
        @if($commande->date_livraison_prevue)
            <p><strong>Date livraison prévue :</strong> {{ \Carbon\Carbon::parse($commande->date_livraison_prevue)->format('d/m/Y') }}</p>
        @endif
        <p><strong>Statut :</strong> <span class="badge bg-{{ [
            'brouillon' => 'secondary',
            'consignation' => 'warning',
            'reserve' => 'info',
            'livree' => 'success',
            'annulee' => 'danger'
        ][$commande->statut] ?? 'dark' }}">{{ ucfirst($commande->statut) }}</span></p>

        @if($commande->wavesoft_piece_id)
            <p><strong>Exportée vers WaveSoft :</strong> Oui (pièce ID : {{ $commande->wavesoft_piece_id }})</p>
        @else
            <p><strong>Exportée vers WaveSoft :</strong> Non</p>
        @endif
    </div>

    <h5 class="mb-3">Articles commandés</h5>

    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>Article</th>
                    <th>Référence</th>
                    <th>Quantité</th>
                    <th>PU HT</th>
                    <th>Remise</th>
                    <th>TVA (%)</th>
                    <th>Total HT</th>
                    <th>Total TTC</th>
                    <th>Statut ligne</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commande->lignesCommande as $ligne)
                    <tr>
                        <td>{{ $ligne->article->designation ?? 'Inconnu' }}</td>
                        <td>{{ $ligne->article->reference ?? '-' }}</td>
                        <td>{{ $ligne->quantite }}</td>
                        <td>{{ number_format($ligne->prix_unitaire_ht, 2, ',', ' ') }} €</td>
                        <td>{{ $ligne->remise_percent }} %</td>
                        <td>{{ $ligne->taux_tva }}%</td>
                        <td>{{ number_format($ligne->montant_ht, 2, ',', ' ') }} €</td>
                        <td>{{ number_format($ligne->montant_ht + $ligne->montant_tva, 2, ',', ' ') }} €</td>
                        <td>
                            <span class="badge bg-{{ [
                                'en_attente' => 'secondary',
                                'reserve' => 'info',
                                'en_consigne' => 'warning',
                                'prepare' => 'primary',
                                'livre' => 'success',
                                'annule' => 'danger'
                            ][$ligne->statut] ?? 'dark' }}">
                                {{ ucfirst(str_replace('_', ' ', $ligne->statut)) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <p><strong>Montant HT :</strong> {{ number_format($commande->montant_ht, 2, ',', ' ') }} €</p>
        <p><strong>Montant TVA :</strong> {{ number_format($commande->montant_tva, 2, ',', ' ') }} €</p>
        @if($commande->remise_percent > 0)
            <p><strong>Remise globale :</strong> {{ $commande->remise_percent }} %</p>
        @elseif($commande->remise_montant > 0)
            <p><strong>Remise globale :</strong> {{ number_format($commande->remise_montant, 2, ',', ' ') }} €</p>
        @endif
        <p><strong>Total TTC :</strong> {{ number_format($commande->montant_ttc, 2, ',', ' ') }} €</p>
    </div>

    @if($commande->notes)
        <div class="alert alert-info mt-4">
            <strong>Notes :</strong><br>
            {{ $commande->notes }}
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('commercial.commandes.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>
@endsection
