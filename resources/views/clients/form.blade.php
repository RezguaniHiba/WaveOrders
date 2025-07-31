@csrf

<div class="mb-3">
    <label for="nom" class="form-label">Nom</label>
    <input type="text" name="nom" class="form-control" value="{{ old('nom', $client->nom ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $client->email ?? '') }}">
</div>

<div class="mb-3">
    <label for="telephone" class="form-label">Téléphone</label>
    <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $client->telephone ?? '') }}">
</div>

<div class="mb-3">
    <label for="adresse" class="form-label">Adresse</label>
    <textarea name="adresse" class="form-control">{{ old('adresse', $client->adresse ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label for="ville" class="form-label">Ville</label>
    <input type="text" name="ville" class="form-control" value="{{ old('ville', $client->ville ?? '') }}">
</div>

<div class="mb-3">
    <label for="code_postal" class="form-label">Code postal</label>
    <input type="text" name="code_postal" class="form-control" value="{{ old('code_postal', $client->code_postal ?? '') }}">
</div>

<div class="mb-3">
    <label for="pays" class="form-label">Pays</label>
    <input type="text" name="pays" class="form-control" value="{{ old('pays', $client->pays ?? 'Maroc') }}">
</div>
