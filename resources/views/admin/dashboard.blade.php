@extends('layouts.app')
@section('content')
<div class="row">
    <!-- Commerciaux -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Commerciaux</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $commerciauxStats['totalCommerciaux'] }}</div>
                        <div class="mt-2">
                            @foreach($commerciauxStats['topCommerciaux'] as $commercial)
                            <div class="text-muted text-sm">
                                <i class="fas fa-star text-warning"></i>
                                {{ $commercial->nom}} ({{ $commercial->total_sales }} ventes)
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Clients</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['totalClients'] }}</div>
                        <div class="mt-2 text-muted text-sm">
                            <i class="fas fa-arrow-up text-success"></i>
                            <span>{{ $stats['newClientsThisMonth'] }} nouveaux ce mois</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CA Mensuel -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            CA Mensuel</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['monthlyRevenue'], 2) }} DH</div>
                        <div class="mt-2 text-muted text-sm">
                            <i class="fas fa-arrow-{{ $monthlyDiff >= 0 ? 'up text-success' : 'down text-danger' }}"></i>
                            <span>{{ number_format(abs($monthlyDiff), 2) }}% vs mois précédent</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <!-- CA Annuel -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            CA Annuel</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['annualRevenue'], 2) }} DH</div>
                        <div class="mt-2 text-muted text-sm">
                            <i class="fas fa-arrow-{{ $annualDiff >= 0 ? 'up text-success' : 'down text-danger' }}"></i>
                            <span>{{ number_format(abs($annualDiff), 2) }}% vs année précédente</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Evolution du CA -->
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Evolution du Chiffre d'Affaires</h6>
            </div>
            <div class="card-body">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
    <!-- Acquisition clients -->
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Nouveaux Clients</h6>
            </div>
            <div class="card-body">
                <canvas id="clientAcquisitionChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json(array_column($revenueData, 'month')),
            datasets: [{
                label: 'Chiffre d\'affaires (DH)',
                data: @json(array_column($revenueData, 'amount')),
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: '#fff',
                pointHoverRadius: 5,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: '#fff',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + ' DH';
                        }
                    }
                }
            }
        }
    });

    // Client Acquisition Chart
    const clientCtx = document.getElementById('clientAcquisitionChart').getContext('2d');
    new Chart(clientCtx, {
        type: 'bar',
        data: {
            labels: @json(array_column($clientAcquisitionData, 'month')),
            datasets: [{
                label: 'Nouveaux clients',
                data: @json(array_column($clientAcquisitionData, 'count')),
                backgroundColor: 'rgba(54, 185, 204, 0.5)',
                borderColor: 'rgba(54, 185, 204, 1)',
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection