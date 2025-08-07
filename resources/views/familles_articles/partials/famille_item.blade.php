@php
    $padding = 15 + (20 * $niveau);
@endphp
<li class="list-group-item border-0 py-2 famille-item" style="padding-left: {{ $padding }}px;">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <span class="text-primary me-2">
                <i class="fas fa-folder{{ $famille->enfants->count() ? '-open' : '' }}"></i>
            </span>
            <span class="fw-medium">{{ $famille->libelle }}</span>
            <span class="badge bg-light text-dark ms-2 border">{{ $famille->articles->count() }}</span>
        </div>

        <div class="btn-group btn-group-sm  gap-2" role="group">
            <a href="{{ route('familles-articles.show', $famille->id) }}" 
               class="btn btn-outline-primary rounded-circle action-btn" 
               title="Voir"
               data-bs-toggle="tooltip">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('familles-articles.edit', $famille->id) }}" 
               class="btn btn-outline-warning rounded-circle action-btn"
               title="Modifier"
               data-bs-toggle="tooltip">
                <i class="fas fa-pencil-alt"></i>
            </a>
            
            <form action="{{ route('familles-articles.destroy', $famille->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="btn btn-outline-danger rounded-circle action-btn"
                        title="Supprimer"
                        data-bs-toggle="tooltip"
                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette famille ?')">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </div>
</li>

@if($famille->enfants && $famille->enfants->count())
    @foreach($famille->enfants as $enfant)
        @include('familles_articles.partials.famille_item', ['famille' => $enfant, 'niveau' => $niveau + 1])
    @endforeach
@endif