@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h3 class="mb-0">
            <i class="fas fa-boxes me-2"></i>Liste des articles
        </h3>
        <div class="d-flex gap-3 flex-wrap">
            <form method="GET" action="{{ route('articles.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" class="form-control form-select-sm" placeholder="Réf. ou désignation" value="{{ request('q') }}">
                </div>
                
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text"><i class="fas fa-folder"></i></span>
                    <select name="famille_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Toutes les familles</option>
                        @php
                            function afficherFamilles($familles, $prefix = '') {
                                foreach ($familles as $famille) {
                                    echo '<option value="'.$famille->id.'"'.(request('famille_id') == $famille->id ? ' selected' : '').'>'.$prefix.$famille->libelle.'</option>';
                                    if ($famille->enfants->count()) {
                                        afficherFamilles($famille->enfants, $prefix . '— ');
                                    }
                                }
                            }
                            afficherFamilles($familles);
                        @endphp
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
            </form>

            <a href="{{ route('articles.create') }}" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                <i class="fas fa-plus"></i> Nouvel article
            </a>
        </div>
    </div>

    @if($articles->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Désignation</th>
                            <th>Prix HT</th>
                            <th>Prix TTC</th>
                            <th>Stocks</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articles as $article)
                        <tr>
                            <td class="fw-bold">{{ $article->reference }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    {{ $article->designation }}
                                    @if (!$article->actif)
                                        <span class="badge bg-danger ms-2">Inactif</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    {{ optional($article->famille)->cheminComplet() }}
                                </small>
                            </td>
                            <td>{{ number_format($article->prix_ht, 2, ',', ' ') }} DH</td>
                            <td>{{ number_format($article->prix_ttc, 2, ',', ' ') }} DH</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-success">Dispo: {{ $article->stock_disponible }}</span>
                                    <span class="badge bg-warning text-dark">Réservé: {{ $article->stock_reserve }}</span>
                                    <span class="badge bg-info">Consigné: {{ $article->stock_consigne }}</span>
                                </div>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('articles.show', $article) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-circle action-btn" 
                                       title="Voir"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('articles.edit', $article) }}" 
                                       class="btn btn-sm btn-outline-warning rounded-circle action-btn" 
                                       title="Modifier"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('articles.destroy', $article) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger rounded-circle action-btn" 
                                                title="Supprimer" 
                                                onclick="return confirm('Confirmer la suppression ?')"
                                                data-bs-toggle="tooltip">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination avec conservation des filtres --}}
    <div class="mt-3 d-flex justify-content-center">
        {{ $articles->withQueryString()->links() }}
    </div>

    @else
    <div class="alert alert-info text-center py-3">
        <i class="fas fa-info-circle me-2"></i>Aucun article trouvé pour vos critères.
    </div>
    @endif
</div>

{{-- Activation des tooltips Bootstrap --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Activer tous les tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<style>
    .action-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .table th {
        white-space: nowrap;
    }
    
    .badge {
        font-weight: 500;
        padding: 4px 8px;
    }
</style>
@endsection