@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Créer une commande</h3>

    <form method="POST" action="{{ route('commandes.store') }}">
        @csrf

        <div class="row">
            {{-- CLIENT --}}
            <div class="col-md-6 mb-3">
                <label for="client_id" class="form-label">Client</label>
                <select name="client_id" id="client_id" class="form-control" required>
                    <option value="">-- Sélectionner un client --</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->nom }}</option>
                    @endforeach
                </select>
            </div>

            {{-- DATE LIVRAISON --}}
            <div class="col-md-6 mb-3">
                <label for="date_livraison_prevue" class="form-label">Date de livraison prévue</label>
                <input type="date" name="date_livraison_prevue" id="date_livraison_prevue" class="form-control">
            </div>
        </div>

        <hr class="my-4">

        {{-- ARTICLES --}}
        <h5 class="mb-3">Articles</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="table-articles">
                <thead class="thead-light">
                    <tr>
                        <th>Article</th>
                        <th>Quantité</th>
                        <th>Remise (%)</th>
                        <th style="width: 50px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="lignes-commande">
                    <tr>
                        <td>
                            <select name="articles[0][article_id]" class="form-control" required>
                                @foreach($articles as $article)
                                    <option value="{{ $article->id }}">
                                        {{ $article->designation }} ({{ $article->reference }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="articles[0][quantite]" class="form-control" min="1" required></td>
                        <td><input type="number" name="articles[0][remise_percent]" class="form-control" min="0" max="100" value="0"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">🗑</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-4">
            <button type="button" class="btn btn-outline-primary btn-sm" id="add-ligne">+ Ajouter un article</button>
        </div>

        {{-- BOUTON --}}
        <div class="form-group">
            <button type="submit" class="btn btn-success">✅ Enregistrer la commande</button>
        </div>
    </form>
</div>

{{-- JS pour ajouter dynamiquement des lignes --}}
<script>
    let ligneIndex = 1;
    document.getElementById('add-ligne').addEventListener('click', function () {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select name="articles[${ligneIndex}][article_id]" class="form-control" required>
                    @foreach($articles as $article)
                        <option value="{{ $article->id }}">{{ $article->designation }} ({{ $article->reference }})</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="articles[${ligneIndex}][quantite]" class="form-control" min="1" required></td>
            <td><input type="number" name="articles[${ligneIndex}][remise_percent]" class="form-control" min="0" max="100" value="0"></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">🗑</button>
            </td>
        `;
        document.getElementById('lignes-commande').appendChild(newRow);
        ligneIndex++;
    });
</script>
@endsection
