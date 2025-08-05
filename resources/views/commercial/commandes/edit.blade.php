@extends('layouts.app')

@section('content')
    <div class="container py-2">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-light-blue text-white py-3">
                <h3 class="h5 mb-0">
                    <i class="fas fa-edit me-2"></i>Modifier la commande #{{ $commande->numero }}
                </h3>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('commandes.update', $commande->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-2">
                        {{-- CLIENT --}}
                        <div class="col-md-6">
                            <label for="client_id" class="form-label fw-semibold">Client <span class="text-danger">*</span></label>
                            <select name="client_id" id="client_id" class="form-select shadow-none" required>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ $commande->client_id == $client->id ? 'selected' : '' }}>
                                        {{ $client->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- DATE DE LIVRAISON --}}
                        <div class="col-md-6">
                            <label for="date_livraison_prevue" class="form-label fw-semibold">Date de livraison prévue</label>
                            <input type="date" name="date_livraison_prevue" id="date_livraison_prevue"
                                   class="form-control shadow-none"
                                   value="{{ $commande->date_livraison_prevue ? \Carbon\Carbon::parse($commande->date_livraison_prevue)->format('Y-m-d') : '' }}">
                        </div>
                    </div>
                    <hr class="my-1 bg-light">{{-- Lignes separateur--}}
                    {{-- ARTICLES --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="h6 mb-0 fw-semibold text-uppercase text-muted">
                            <i class="fas fa-boxes me-2"></i>Articles
                        </h5>
                        <button type="button" class="btn btn-primary btn-sm" id="add-ligne">
                            <i class="fas fa-plus me-1"></i>Ajouter un article
                        </button>
                    </div>
                    <div class="table-responsive mb-1">
                        <table class="table table-hover" id="table-articles">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">Article</th>
                                    <th class="border-0">Quantité</th>
                                    <th class="border-0">Remise</th>
                                    <th class="border-0">Statut</th>
                                    <th class="border-0 text-center" style="width: 60px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="lignes-commande">
                                @foreach($commande->lignesCommande as $index => $ligne)
                                <tr class="align-middle">
                                    <td>
                                        <input type="hidden" name="articles[{{ $index }}][id]" value="{{ $ligne->id }}">
                                        <select name="articles[{{ $index }}][article_id]" class="form-select shadow-none" required>
                                            @foreach($articles as $article)
                                                <option value="{{ $article->id }}" {{ $ligne->article_id == $article->id ? 'selected' : '' }}>
                                                    {{ $article->designation }} ({{ $article->reference }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="articles[{{ $index }}][quantite]" class="form-control shadow-none" min="1" value="{{ $ligne->quantite }}" required>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" name="articles[{{ $index }}][remise_percent]" class="form-control shadow-none" min="0" max="100" value="{{ $ligne->remise_percent ?? 0 }}">
                                            <span class="input-group-text bg-white">%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <select name="articles[{{ $index }}][statut]" class="form-select shadow-none" required>
                                             <option value="en_attente" {{ $ligne->statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                            <option value="reserve" {{ $ligne->statut == 'reserve' ? 'selected' : '' }}>Réservé</option>
                                            <option value="en_consigne" {{ $ligne->statut == 'en_consigne' ? 'selected' : '' }}>En consigne</option>
                                            <option value="prepare" {{ $ligne->statut == 'prepare' ? 'selected' : '' }}>Preparé</option>
                                            <option value="livre" {{ $ligne->statut == 'livre' ? 'selected' : '' }}>Livré</option>
                                            <option value="annule" {{ $ligne->statut == 'annule' ? 'selected' : '' }}>Annulé</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="this.closest('tr').remove()">
                                            <i class="fas fa-trash-alt fa-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <hr class="my-1 bg-light">
                    {{-- NOTES DE COMMANDE --}}
                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Notes de commande</label>
                        <textarea name="notes" class="form-control shadow-none" rows="1" placeholder="Ajoutez des remarques ou instructions spécifiques...">{{ old('notes', $commande->notes) }}</textarea>
                    </div>
                    {{-- BOUTONS --}}
                    <div class="d-flex justify-content-end gap-3 pt-3">
                        <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

{{-- SCRIPT pour ajouter dynamiquement des lignes --}}
<script>
    let ligneIndex = {{ $commande->lignesCommande->count() }};
    const optionsArticles = `@foreach($articles as $article)
        <option value="{{ $article->id }}">{{ $article->designation }} ({{ $article->reference }})</option>
    @endforeach`;

    document.getElementById('add-ligne').addEventListener('click', () => {
        const newRow = document.createElement('tr');
        newRow.className = 'align-middle';
        newRow.innerHTML = `
            <td>
                <select name="articles[${ligneIndex}][article_id]" class="form-select shadow-none" required>
                    ${optionsArticles}
                </select>
            </td>
            <td>
                <input type="number" name="articles[${ligneIndex}][quantite]" class="form-control shadow-none" min="1" value="1" required>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" name="articles[${ligneIndex}][remise_percent]" class="form-control shadow-none" min="0" max="100" value="0">
                    <span class="input-group-text bg-white">%</span>
                </div>
            </td>
            <td>
                <select name="articles[${ligneIndex}][statut]" class="form-select shadow-none" required>
                    <option value="en_attente">En attente</option>
                    <option value="reserve">Réservé</option>
                    <option value="en_consigne">En consigne</option>
                    <option value="prepare">Préparé</option>
                    <option value="livre">Livré</option>
                    <option value="annule">Annulé</option>
                </select>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="this.closest('tr').remove()">
                    <i class="fas fa-trash-alt fa-xs"></i>
                </button>
            </td>
        `;
        document.getElementById('lignes-commande').appendChild(newRow);
        ligneIndex++;
    });
</script>

<style>
    .bg-light-blue {
        background-color: #1e3a8a;
    }
    
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .form-control, .form-select, .input-group-text {
        border-radius: 6px;
        transition: all 0.2s;
    }
    
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(30, 58, 138, 0.1);
        border-color: #1e3a8a;
    }
    
    .shadow-none {
        box-shadow: none !important;
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    
    .rounded-circle {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .rounded-pill {
        padding: 0.375rem 1.25rem;
    }
    
    .border-light {
        border-color: #f8f9fa !important;
    }
</style>
@endsection