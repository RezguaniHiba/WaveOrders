@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h2 class="mb-0">📦 Mes Commandes</h2>

        <div class="d-flex gap-3 flex-wrap">
            <form method="GET" action="{{ route('commercial.commandes.index') }}" class="d-flex gap-2 align-items-center me-2">
                
                {{-- Liste déroulante client --}}
                <select name="client_id" class="form-select form-select-sm" aria-label="Filtrer par client">
                    <option value="">Tous les clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" @if(request('client_id') == $client->id) selected @endif>
                            {{ $client->nom }}
                        </option>
                    @endforeach
                </select>

                {{-- Liste déroulante statut --}}
                <select name="statut" class="form-select form-select-sm" aria-label="Filtrer par statut">
                    <option value="">Tous statuts</option>
                    @foreach(['brouillon', 'consignation', 'reserve', 'livree', 'annulee'] as $statut)
                        <option value="{{ $statut }}" @if(request('statut') === $statut) selected @endif>
                            {{ ucfirst($statut) }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
            </form>

            <a href="{{ route('commandes.create') }}" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                <i class="fa fa-plus"></i> Nouvelle commande
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($commandes->count() > 0)
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
                @php
                    $couleurs = [
                        'brouillon' => 'secondary',
                        'consignation' => 'warning',
                        'reserve' => 'info',
                        'livree' => 'success',
                        'annulee' => 'danger'
                    ];
                @endphp

                @foreach ($commandes as $commande)
                <tr>
                    <td class="text-center font-weight-bold">{{ $commande->numero }}</td>
                    <td>{{ $commande->client->nom ?? 'Inconnu' }}</td>
                    <td class="text-center">{{ $commande->date_commande->format('d/m/Y') }}</td>
                    <td class="text-center">
                        <span class="badge bg-{{ $couleurs[$commande->statut] ?? 'dark' }}" 
                            data-bs-toggle="tooltip" title="Statut : {{ ucfirst($commande->statut) }}">
                            {{ ucfirst($commande->statut) }}
                        </span>
                    </td>
                    <td class="text-end text-nowrap">
                        {{ number_format($commande->montant_ttc, 2, ',', ' ') }} €
                    </td>
                    <td class="text-center">
                        @if($commande->wavesoft_piece_id)
                            <span class="text-success" title="Commande exportée">
                                <i class="fa fa-check-circle"></i>
                            </span>
                        @else
                            <span class="text-muted" title="Non exportée">
                                <i class="fa fa-times-circle"></i>
                            </span>
                        @endif
                    </td>
                    <td class="text-center text-nowrap">
                        <a href="{{ route('commandes.show', $commande->id) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                            <i class="fa fa-eye"></i>
                        </a>
                        @if($commande->statut === 'brouillon')
                        <a href="{{ route('commandes.edit', $commande->id) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                            <i class="fa fa-pencil"></i>
                        </a>
                        @endif
                        @if(!in_array($commande->statut, ['livree', 'annulee']))
                        <form action="{{ route('commandes.destroy', $commande->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Annuler" onclick="return confirm('Confirmer l'annulation ?')">
                                <i class="fa fa-times"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3 d-flex justify-content-center">
        {{ $commandes->withQueryString()->links() }}
    </div>

    @else
    <div class="alert alert-info text-center">
        Aucune commande trouvée pour vos critères.
    </div>
    @endif

</div>

{{-- Activation des tooltips Bootstrap --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection