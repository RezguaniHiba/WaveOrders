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
        <a class="navbar-brand" href="#">🧾 WaveOrders</a>
        <div class="ml-auto">
            <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-light">Déconnexion</a>
        </div>
    </nav>

    <main class="container">
                    {{-- Messages flash (succès ou erreur) --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center py-3 px-4 border-0 shadow-sm" role="alert" style="background-color: #f0faf1; border-left: 4px solid #28a745;">
                <i class="fas fa-check-circle me-3 text-success"></i>
                <div class="flex-grow-1">
                    {{ session('success') }}
                </div>
                <button type="button" class="close ms-3" data-dismiss="alert" aria-label="Fermer" style="background: none; border: none; font-size: 1.5rem; line-height: 1; opacity: 0.7;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
      {{-- Erreur de validation detecter lors de validation de formulaire via validate les erreur seront stocker dans $errors--}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show d-flex py-3 px-4 border-0 shadow-sm" role="alert" style="background-color: #fdf3f3; border-left: 4px solid #dc3545;">
                <i class="fas fa-exclamation-circle me-3 text-danger align-self-start mt-1"></i>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <strong>Erreur:</strong>
                        <button type="button" class="close ml-2" data-dismiss="alert" aria-label="Fermer" style="background: none; border: none; font-size: 1.5rem; line-height: 1; opacity: 0.7;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <ul class="mb-0 ps-3" style="list-style-type: disc;">
                        @foreach ($errors->all() as $error)
                            <li class="mb-1">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
{{--Erreur personalise ->logique metier return avec par exzmple redirect()->back()->with('error', 'msg d'err...');--}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-3 px-4 border-0 shadow-sm" role="alert" style="background-color: #fdf3f3; border-left: 4px solid #dc3545;">
                <i class="fas fa-exclamation-triangle me-3 text-danger"></i>
                <div class="flex-grow-1">
                    {{ session('error') }}
                </div>
                <button type="button" class="close ms-3" data-dismiss="alert" aria-label="Fermer" style="background: none; border: none; font-size: 1.5rem; line-height: 1; opacity: 0.7;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center text-muted mt-5 mb-3">
       {{-- &copy; {{ date('Y') }} - MonApp. Tous droits réservés.--}}
    </footer>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>