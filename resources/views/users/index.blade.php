@extends('layouts.app')

@section('content')
<div class="container-fluid content-container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h3 class="mb-0">
            <i class="fas fa-users me-2"></i>Liste des utilisateurs
        </h3>

        <div class="d-flex gap-3 flex-wrap">
            <form method="GET" action="{{ route('users.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control form-select-sm" placeholder="Rechercher..." value="{{ request('search') }}">
                </div>

                <div class="input-group input-group-sm" style="width: 160px;">
                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                    <select name="role" class="form-select form-select-sm">
                        <option value="">Tous rôles</option>
                        <option value="admin" @if(request('role') == 'admin') selected @endif>Admin</option>
                        <option value="commercial" @if(request('role') == 'commercial') selected @endif>Commercial</option>
                    </select>
                </div>

                <div class="input-group input-group-sm" style="width: 160px;">
                    <span class="input-group-text"><i class="fas fa-circle"></i></span>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Tous statuts</option>
                        <option value="active" @if(request('status') == 'active') selected @endif>Actif</option>
                        <option value="inactive" @if(request('status') == 'inactive') selected @endif>Inactif</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
            </form>

            <a href="{{ route('users.create') }}" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                <i class="fas fa-plus"></i> Nouveau
            </a>
        </div>
    </div>

    @if($users->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap">Nom</th>
                            <th class="text-nowrap">Email</th>
                            <th class="text-nowrap">Téléphone</th>
                            <th class="text-nowrap">Rôle</th>
                            <th class="text-nowrap">Statut</th>
                            <th class="text-nowrap">Date de création</th>
                            <th class="text-center text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="fw-bold text-truncate" style="max-width: 150px;">{{ $user->nom }}</td>
                            <td class="text-truncate" style="max-width: 150px;" title="{{ $user->email }}">{{ $user->email }}</td>
                            <td class="text-nowrap">{{ $user->telephone ?? '-' }}</td>
                            <td class="text-nowrap">
                                <span class="badge 
                                    {{ $user->role === 'admin' ? 'bg-danger' : 
                                      ($user->role === 'commercial' ? 'bg-primary' : 'bg-secondary') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <span class="badge {{ $user->actif ? 'bg-success' : 'bg-warning' }}">
                                    <i class="fas fa-{{ $user->actif ? 'check' : 'times' }} me-1"></i>
                                    {{ $user->actif ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="text-nowrap">{{ $user->date_creation->format('d/m/y') }}</td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('users.show', $user) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-circle action-btn" 
                                       title="Voir"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" 
                                       class="btn btn-sm btn-outline-warning rounded-circle action-btn" 
                                       title="Modifier"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
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

    <div class="mt-3 d-flex justify-content-center">
        {{ $users->withQueryString()->links() }}
    </div>

    @else
    <div class="alert alert-info text-center py-2">
        <i class="fas fa-info-circle me-1"></i>Aucun utilisateur trouvé
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialisation des tooltips
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(el => new bootstrap.Tooltip(el));
        
        // Smooth page transitions
        document.querySelectorAll('a.nav-link, a.dropdown-item').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('href') && !this.getAttribute('target')) {
                    e.preventDefault();
                    document.body.style.opacity = '0.8';
                    setTimeout(() => {
                        window.location.href = this.getAttribute('href');
                    }, 150);
                }
            });
        });
    });
</script>

<style>
    .content-container {
        padding-left: var(--content-padding-x);
        padding-right: var(--content-padding-x);
    }

    .table {
        font-size: 0.85rem;
    }

    .table th,
    .table td {
        padding: 0.5rem;
        vertical-align: middle;
    }

    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .badge {
        font-weight: 500;
        padding: 0.35em 0.5em;
    }

    .action-btn {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .action-btn i {
        font-size: 0.75rem;
    }

    @media (max-width: 768px) {
        .content-container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .d-flex.flex-wrap {
            gap: 0.5rem !important;
        }

        .input-group {
            width: 100% !important;
        }
    }
</style>
@endsection