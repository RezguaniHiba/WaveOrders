@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">📦 Mes Commandes</h2>
        <a href="{{ route('commandes.create') }}" class="btn btn-success">
            <i class="fa fa-plus mr-1"></i> Nouvelle commande
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped table-sm align-middle">
            <thead class="thead-dark text-center">
                <tr>
                    <th>N°</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Total TTC</th>
                    <th>Exporté ?</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($commandes as $commande)
                    <tr>
                        <td class="text-center font-weight-bold">{{ $commande->numero }}</td>
                        <td>{{ $commande->client->nom ?? 'Inconnu' }}</td>
                        <td class="text-center">{{ $commande->date_commande->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @php
                                $couleurs = [
                                    'brouillon' => 'secondary',
                                    'consignation' => 'warning',
                                    'reserve' => 'info',
                                    'livree' => 'success',
                                    'annulee' => 'danger'
                                ];
                            @endphp
                            <span class="badge badge-{{ $couleurs[$commande->statut] ?? 'dark' }}">
                                {{ ucfirst($commande->statut) }}
                            </span>
                        </td>
                        <td class="text-right text-nowrap">
                            {{ number_format($commande->montant_ttc, 2, ',', ' ') }} €
                        </td>
                        <td class="text-center">
                            @if($commande->wavesoft_piece_id)
                                <span class="text-success"><i class="fa fa-check-circle"></i></span>
                            @else
                                <span class="text-muted"><i class="fa fa-times-circle"></i></span>
                            @endif
                        </td>
                        <td class="text-center text-nowrap">
                            <a href="#" class="btn btn-sm btn-outline-primary" title="Voir">
                                <i class="fa fa-eye"></i>
                            </a>
                            @if($commande->statut === 'brouillon')
                                <a href="#" class="btn btn-sm btn-outline-warning" title="Modifier">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endif
                            @if(!in_array($commande->statut, ['livree', 'annulee']))
                                <form action="#" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Annuler" onclick="return confirm('Confirmer l’annulation ?')">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Aucune commande trouvée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
