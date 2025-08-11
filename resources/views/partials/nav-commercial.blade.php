<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('commercial.dashboard') }}">
            <img src="{{ asset('images/WaveOrders.png') }}" alt="Logo WaveOrders" style="height: 26px;">
            <span style="color: #14145a;" style="font-size: 1.1rem;">WaveOrders</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#commercialNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars" style="font-size: 1.1rem;"></i>
        </button>

        <div class="collapse navbar-collapse" id="commercialNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('commercial.dashboard')) active @endif" href="{{ route('commercial.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-1" style="font-size: 0.9rem;"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('clients.*')) active @endif" href="{{ route('clients.index') }}">
                        <i class="fas fa-users me-1" style="font-size: 0.9rem;"></i>
                        <span>Clients</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('commandes.*')) active @endif" href="{{ route('commandes.index') }}">
                        <i class="fas fa-shopping-cart me-1" style="font-size: 0.9rem;"></i>
                        <span>Commandes</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('reglements.*') || request()->routeIs('commandes.reglements.*')) active @endif" href="{{ route('reglements.index') }}">
                        <i class="fas fa-money-bill-wave me-1" style="font-size: 0.9rem;"></i>
                        <span>Règlements</span>
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle @if(request()->routeIs('familles-articles.*') || request()->routeIs('articles.*')) active @endif" href="#" id="articlesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-boxes me-1" style="font-size: 0.9rem;"></i>
                        <span>Articles</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="articlesDropdown">
                        <li>
                            <a class="dropdown-item @if(request()->routeIs('familles-articles.*')) active @endif" href="{{ route('familles-articles.index') }}">
                                <i class="fas fa-layer-group me-1" style="font-size: 0.9rem;"></i>
                                Familles d'articles
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item @if(request()->routeIs('articles.*')) active @endif" href="{{ route('articles.index') }}">
                                <i class="fas fa-box me-1" style="font-size: 0.9rem;"></i>
                                Articles
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <div class="navbar-right-container d-flex align-items-center ms-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt" style="font-size: 0.9rem;"></i>
                        <span class="d-none d-sm-inline">Déconnexion</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

