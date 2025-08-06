<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="reference" class="form-label fw-medium">Référence <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="fas fa-barcode text-primary"></i></span>
            <input type="text" name="reference" id="reference" class="form-control"
                   value="{{ old('reference', $article->reference ?? '') }}" required
                   placeholder="Référence unique">
        </div>
    </div>

    <div class="col-md-6">
        <label for="designation" class="form-label fw-medium">Désignation <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="fas fa-tag text-primary"></i></span>
            <input type="text" name="designation" id="designation" class="form-control"
                   value="{{ old('designation', $article->designation ?? '') }}" required
                   placeholder="Nom de l'article">
        </div>
    </div>
    
    <div class="col-md-6">
        <label for="famille_id" class="form-label fw-medium">Famille</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="fas fa-folder-tree text-primary"></i></span>
            <select name="famille_id" id="famille_id" class="form-select">
                <option value="">-- Aucune --</option>
                @foreach ($familles as $famille)
                    <option value="{{ $famille->id }}"
                        {{ old('famille_id', $article->famille_id ?? '') == $famille->id ? 'selected' : '' }}>
                        {{ $famille->cheminComplet() }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <label for="description" class="form-label fw-medium">Description</label>
        <textarea name="description" id="description" rows="1" class="form-control"
                  placeholder="Description détaillée...">{{ old('description', $article->description ?? '') }}</textarea>
    </div>

    <div class="col-md-4">
        <label for="prix_ht" class="form-label fw-medium">Prix HT <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light">DH</span>
            <input type="number" name="prix_ht" id="prix_ht" class="form-control" step="0.01"
                   value="{{ old('prix_ht', $article->prix_ht ?? '') }}" required
                   placeholder="0.00">
        </div>
    </div>

    <div class="col-md-4">
        <label for="taux_tva" class="form-label fw-medium">Taux TVA (%) <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light">%</span>
            <input type="number" name="taux_tva" id="taux_tva" class="form-control" step="0.01"
                   value="{{ old('taux_tva', $article->taux_tva ?? 20) }}" required>
        </div>
    </div>

    <div class="col-md-4">
        <label for="unite" class="form-label fw-medium">Unité</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="fas fa-balance-scale text-primary"></i></span>
            <input type="text" name="unite" id="unite" class="form-control"
                   value="{{ old('unite', $article->unite ?? '') }}"
                   placeholder="kg, L, pièce...">
        </div>
    </div>

    <div class="col-md-4">
        <label for="stock_disponible" class="form-label fw-medium">Stock disponible</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="fas fa-boxes text-primary"></i></span>
            <input type="number" name="stock_disponible" id="stock_disponible" class="form-control"
                   value="{{ old('stock_disponible', $article->stock_disponible ?? 0) }}">
        </div>
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch">
            <input type="checkbox" name="actif" id="actif" class="form-check-input" role="switch" value="1"
                   {{ old('actif', $article->actif ?? true) ? 'checked' : '' }}>
            <label for="actif" class="form-check-label fw-medium">Article actif</label>
        </div>
    </div>
</div>