@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    Situation Financière : {{ $client->nom }}
                </h4>
                <a href="{{ route($routePrefix.'show', $client) }}" class="btn btn-sm btn-light">
                    <i class="fas fa-arrow-left me-1"></i> Retour fiche
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Résumé chiffré -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Commandes</h6>
                            <p class="h3 text-primary">{{ number_format($totalCommandes, 2, ',', ' ') }} DH</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Réglé</h6>
                            <p class="h3 text-success">{{ number_format($totalReglements, 2, ',', ' ') }} DH</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-{{ $solde > 0 ? 'danger' : 'success' }}">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Solde</h6>
                            <p class="h3 text-{{ $solde > 0 ? 'danger' : 'success' }}">
                                {{ number_format($solde, 2, ',', ' ') }} DH
                            </p>
                        </div>
                    </div>
                </div>
            </div>
<div class="card mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-list-ul me-2"></i> Commandes et paiements
        </h5>
    </div>
    <div class="card-body">
        @if($commandes->count() > 0)
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Commande</th>
                        <th>Date</th>
                        <th class="text-end">Montant TTC</th>
                        <th class="text-end">Montant payé</th>
                        <th class="text-end">Reste à payer</th>
                        <th>Origine</th>
                        <th>Mode</th>
                        <th>Facture tiers</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($commandes as $cmd)
                        <!-- Ligne commande -->
                        <tr class="table-primary">
                            <td>
                                <a href="{{ route('commandes.show', $cmd['id']) }}" class="fw-bold text-dark">
                                    #{{ $cmd['numero'] }}
                                </a>
                            </td>
                            <td>{{ $cmd['date']->format('d/m/Y') }}</td>
                            <td class="text-end">{{ number_format($cmd['montant_ttc'], 2, ',', ' ') }} DH</td>
                            <td class="text-end text-success">{{ number_format($cmd['montant_paye'], 2, ',', ' ') }} DH</td>
                            <td class="text-end text-{{ $cmd['reste'] > 0 ? 'danger' : 'success' }}">
                                {{ number_format($cmd['reste'], 2, ',', ' ') }} DH
                            </td>
                            <td colspan="3"></td>
                        </tr>

                        <!-- Paiements liés -->
                        @forelse($cmd['reglements'] as $reg)
                            <tr>
                                <td></td>
                                <td>{{ $reg['date']->format('d/m/Y') }}</td>
                                <td colspan="2" class="text-end fw-bold">{{ number_format($reg['montant'], 2, ',', ' ') }} DH</td>
                                <td></td>
                                <td>{{ $reg['origine'] }}</td>
                                <td>{{ $reg['mode'] }}</td>
                                <td>
                                    @if($reg['facture_tiers'])
                                        <span class="badge bg-warning text-dark">Oui</span>
                                    @else
                                        <span class="badge bg-success">Non</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td></td>
                                <td colspan="7" class="text-muted fst-italic">Aucun paiement enregistré</td>
                            </tr>
                        @endforelse
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-info text-center mb-0">
            <i class="fas fa-info-circle me-2"></i>Aucune commande enregistrée
        </div>
        @endif
    </div>
</div>


            
            </div>
        </div>
    </div>
</div>
@endsection