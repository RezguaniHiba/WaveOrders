@csrf
@php
    use Illuminate\Support\Facades\Auth;
@endphp

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="nom" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
        <input type="text" name="nom" class="form-control shadow-none" 
               value="{{ old('nom', $client->nom ?? '') }}" required>
    </div>
    
    <div class="col-md-6">
        <label for="email" class="form-label fw-semibold">Email</label>
        <input type="email" name="email" class="form-control shadow-none" 
               value="{{ old('email', $client->email ?? '') }}">
    </div>
    
    <div class="col-md-6">
        <label for="telephone" class="form-label fw-semibold">Téléphone</label>
        <input type="text" name="telephone" class="form-control shadow-none" 
               value="{{ old('telephone', $client->telephone ?? '') }}">
    </div>
    
    <div class="col-md-6">
        <label for="adresse" class="form-label fw-semibold">Adresse</label>
        <textarea name="adresse" class="form-control shadow-none" rows="2" style="min-height: 38px">{{ old('adresse', $client->adresse ?? '') }}</textarea>
    </div>
    
    <div class="col-md-4">
        <label for="ville" class="form-label fw-semibold">Ville</label>
        <input type="text" name="ville" class="form-control shadow-none" 
               value="{{ old('ville', $client->ville ?? '') }}">
    </div>
    
    <div class="col-md-2">
        <label for="code_postal" class="form-label fw-semibold">Code postal</label>
        <input type="text" name="code_postal" class="form-control shadow-none" 
               value="{{ old('code_postal', $client->code_postal ?? '') }}">
    </div>
    
    <div class="col-md-6">
        <label for="pays" class="form-label fw-semibold">Pays</label>
        <input type="text" name="pays" class="form-control shadow-none" 
               value="{{ old('pays', $client->pays ?? 'Maroc') }}">
    </div>
    
    @if (Auth::user()->role === 'admin')
    <div class="col-md-6">
        <label for="commercial_id" class="form-label fw-semibold">Commercial attitré</label>
        <select name="commercial_id" class="form-select shadow-none">
            <option value="">-- Sélectionner un commercial --</option>
            @foreach ($commerciaux as $commercial)
                <option value="{{ $commercial->id }}"
                    {{ old('commercial_id', $client->commercial_id ?? '') == $commercial->id ? 'selected' : '' }}>
                    {{ $commercial->nom }} ({{ $commercial->email }})
                </option>
            @endforeach
        </select>
    </div>
    @endif
</div>