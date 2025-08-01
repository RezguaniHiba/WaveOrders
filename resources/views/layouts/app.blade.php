<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    @yield('styles')

    <title>@yield('title', 'Gestion Commandes')</title>
    <link rel="icon" type="image/jpg" href="{{ asset('favicon.jpg') }}">

    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
     <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <a class="navbar-brand" href="#">🧾 Gestion Commandes</a>
        <div class="ml-auto">
            <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-light">Déconnexion</a>
        </div>
    </nav>

    <main class="container">
            {{-- Messages flash (succès ou erreur) --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif 
        @yield('content')
    </main>

    <footer class="text-center text-muted mt-5 mb-3">
        &copy; {{ date('Y') }} - MonApp. Tous droits réservés.
    </footer>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>