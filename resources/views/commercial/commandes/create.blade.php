@extends('layouts.app')

@section('content')
<div class="container">
            <h3 class="mb-0 text-primary">
                <i class="fas fa-file-invoice me-2"></i>Créer une commande
            </h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('commandes.store') }}">
                @csrf

                <div class="row g-3">
                    {{-- CLIENT --}}
                    <div class="col-md-6">
                        <label for="client_id" class="form-label fw-bold">Client</label>
                        <select name="client_id" id="client_id" class="form-select" required>
                            <option value="">-- Sélectionner un client --</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- DATE LIVRAISON --}}
                    <div class="col-md-6">
                        <label for="date_livraison_prevue" class="form-label fw-bold">Date de livraison prévue</label>
                        <input type="date" name="date_livraison_prevue" id="date_livraison_prevue" class="form-control">
                    </div>
                </div>

                <hr class="my-4">
                {{-- ARTICLES --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-box-open me-2"></i>Articles
                    </h5>
                    <button type="button" class="btn btn-primary btn-sm" id="add-ligne">
                        <i class="fas fa-plus me-1"></i>Ajouter un article
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="table-articles">
                        <thead class="table-light">
                            <tr>
                                <th>Article</th>
                                <th>Quantité</th>
                                <th>Remise (%)</th>
                                <th>Statut</th>
                                <th style="width: 60px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="lignes-commande">
                            <tr>
                                <td>
                                    <select name="articles[0][article_id]" class="form-select" required>
                                        @foreach($articles as $article)
                                            <option value="{{ $article->id }}">{{ $article->designation }} ({{ $article->reference }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="articles[0][quantite]" class="form-control" min="1" required>
                                </td>
                                <td>
                                    <input type="number" name="articles[0][remise_percent]" class="form-control" min="0" max="100" value="0">
                                </td>
                                <td>
                                    <select name="articles[0][statut]" class="form-select" required>
                                        <option value="en_attente">En attente</option>
                                        <option value="reserve">Réservé</option>
                                        <option value="en_cosigne">En consigne</option>
                                        <option value="prepare">Préparé</option>
                                        <option value="livre">Livré</option>
                                        <option value="annule">Annulé</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm px-2" onclick="this.closest('tr').remove()">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- BOUTONS --}}
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary me-md-2">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save me-2"></i>Enregistrer la commande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT pour ajouter dynamiquement des lignes --}}
<script>
let ligneIndex = 1;
const optionsArticles = `@foreach($articles as $article)
    <option value="{{ $article->id }}">{{ $article->designation }} ({{ $article->reference }})</option>
@endforeach`;

document.getElementById('add-ligne').addEventListener('click', () => {
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>
            <select name="articles[${ligneIndex}][article_id]" class="form-select" required>
                ${optionsArticles}
            </select>
        </td>
        <td>
            <input type="number" name="articles[${ligneIndex}][quantite]" class="form-control" min="1" required>
        </td>
        <td>
            <input type="number" name="articles[${ligneIndex}][remise_percent]" class="form-control" min="0" max="100" value="0">
        </td>
        <td>
            <select name="articles[${ligneIndex}][statut]" class="form-select" required>
                <option value="en_attente">En attente</option>
                <option value="reserve">Réservé</option>
                <option value="en_consigne">En consigne</option>
                <option value="prepare">Préparé</option>
                <option value="livre">Livré</option>
                <option value="annule">Annulé</option>
            </select>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm px-2" onclick="this.closest('tr').remove()">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    `;
    document.getElementById('lignes-commande').appendChild(newRow);
    ligneIndex++;
});
</script>

{{-- STYLES --}}
<style>
    .card {
        border-radius: 0.5rem;
    }
    .form-select, .form-control {
        padding: 0.375rem 0.75rem;
    }
    .table th {
        font-weight: 600;
        font-size: 0.85rem;
    }
    .table td {
        vertical-align: middle;
        padding: 0.75rem;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
    }
    .shadow-lg {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
@endsection
