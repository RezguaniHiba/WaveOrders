@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Créer une commande</h3>

    <form method="POST" action="{{ route('commandes.store') }}">
        @csrf

        {{-- CLIENT --}}
        <div class="form-group">
            <label>Client</label>
            <select name="client_id" class="form-control" required>
                <option value="">-- Sélectionner un client --</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->nom }}</option>
                @endforeach
            </select>
        </div>

        {{-- DATE LIVRAISON --}}
        <div class="form-group">
            <label>Date de livraison prévue</label>
            <input type="date" name="date_livraison_prevue" class="form-control">
        </div>

        <hr>

        {{-- ARTICLES --}}
        <h5>Articles</h5>
        <table class="table table-bordered" id="table-articles">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Quantité</th>
                    <th>Remise (%)</th>
                    <th>Actions</th>
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
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">🗑</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="add-ligne">+ Ajouter un article</button>

        {{-- BOUTON --}}
        <div class="form-group">
            <button type="submit" class="btn btn-success">Enregistrer la commande</button>
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
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">🗑</button>
            </td>
        `;
        document.getElementById('lignes-commande').appendChild(newRow);
        ligneIndex++;
    });
</script>
@endsection
