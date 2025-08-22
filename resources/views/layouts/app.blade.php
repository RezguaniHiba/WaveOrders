<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>@yield('title', 'WaveOrders - Gestion Commandes')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">


    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #6c5ce7;
            --primary-light: #a29bfe;
            --secondary-color: #00cec9;
            --dark-color: #2d3436;
            --light-color: #f8f9fa;
            --background-color: #f9f9ff;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --success-color: #28a745;
            --danger-color: #dc3545;
            --transition-speed: 0.25s;
              --content-padding-x: 1rem; /* Valeur par défaut */
    
            @media (min-width: 768px) {
                --content-padding-x: 1.5rem; /* Pour desktop */
            }
            
            @media (min-width: 1200px) {
                --content-padding-x: 2rem; /* Pour grands écrans */
            }

         }   
        
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: var(--background-color);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        main.container {
            padding-left: var(--content-padding-x);
            padding-right: var(--content-padding-x);
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
            max-width: 100%;
        }            
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            padding: 0.5rem 1rem;
            transition: all var(--transition-speed) ease;
            position: relative; 
            overflow: visible;  /* S'assurer que tout est visible */

        }
        .navbar-collapse {
            flex-basis: auto !important; /* Empêche l'expansion excessive */
            flex-grow: 0 !important;
        }
        /* Conteneur des éléments de droite */
        .navbar-right-container {
            display: flex;
            align-items: center;
            margin-left: auto; /* Pousse vers la droite */
            flex-shrink: 0; /* Empêche le rétrécissement */
        }
    
        .navbar-brand {
            font-weight: 600;
            display: flex;
            align-items: center;
            color: var(--dark-color);
            font-size: 0.95rem;
            transition: transform var(--transition-speed) ease;
        }
        
        .navbar-brand:hover {
            transform: translateX(-2px);
        }
        
        .navbar-brand img {
            height: 30px;
            margin-right: 10px;
            transition: transform var(--transition-speed) ease;
        }
        
        .navbar-brand:hover img {
            transform: rotate(-5deg);
        }
        
        .nav-link {
            font-size: 0.85rem;
           /* font-weight: 500;*/
            padding: 0.4rem 0.7rem;
            margin: 0 3px;
            font-size: 0.92rem;
            color: var(--dark-color);
            transition: all var(--transition-speed) ease;
            position: relative;
            border-radius: 6px;
            display: flex;
            align-items: center;
        }
        .navbar-nav {
            flex-wrap: nowrap; /* Empêche le retour à la ligne */
            white-space: nowrap; /* Garde tout sur une ligne */
        }
        .nav-link:hover, 
        .nav-link.active {
            color: var(--primary-color);
            background-color: rgba(108, 92, 231, 0.08);
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 3px;
            background-color: var(--primary-color);
            border-radius: 3px;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: var(--card-shadow);
            border-radius: 10px;
            padding: 0.5rem 0;
            margin-top: 8px;
            font-size: 0.85rem;
            animation: fadeIn var(--transition-speed) ease-out;
            position: absolute;
            right: 0;
            left: auto; /* Alignement à droite */
            min-width: 200px; /* Largeur minimale */
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .dropdown-item {
            padding: 0.4rem 1rem;
            transition: all var(--transition-speed) ease;
            color: var(--dark-color);
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .dropdown-item i {
            width: 20px;
            margin-right: 8px;
            text-align: center;
        }
        
        .dropdown-item:hover, 
        .dropdown-item:focus {
            background-color: rgba(108, 92, 231, 0.08);
            color: var(--primary-color);
            padding-left: 1.5rem;
        }
        
        .dropdown-item.active {
            background-color: var(--primary-color) !important;
            color: white !important;
        }
        
        .navbar-toggler {
            padding: 0.35rem 0.5rem;
            border: none;
            box-shadow: none !important;
        }
        
        .navbar-toggler:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(108, 92, 231, 0.3);
        }
        
        .btn-logout {
            background-color: transparent;
            border: 2px solid var(--danger-color);
            color: var(--danger-color);
            font-weight: 600;
            padding: 0.3rem 0.7rem; 
            border-radius: 8px;
            transition: all var(--transition-speed) ease;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
        }
        
        .btn-logout:hover {
            background-color: rgba(220, 53, 69, 0.1);
            transform: translateY(-1px);
        }
        
        .btn-logout i {
            margin-right: 5px;
        }
        
        .user-greeting {
            color: var(--dark-color);
            font-weight: 500;
            font-size: 0.85rem;
            margin-right: 1rem;
            white-space: nowrap;
        }
        
        @media (max-width: 991.98px) {
              main.container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
                .navbar-collapse {
                padding: 1rem 0;
                border-top: 1px solid rgba(0, 0, 0, 0.05);
                margin-top: 0.5rem;
            }
            
            .nav-link {
                margin: 2px 0;
                padding: 0.6rem 1rem;
            }
            
            .nav-link.active::after {
                display: none;
            }
            
            .dropdown-menu {
                margin-left: 1rem;
                box-shadow: none;
                border-left: 2px solid rgba(108, 92, 231, 0.2);
                border-radius: 0 8px 8px 0;
            }
        }
    </style>
    
    @yield('styles')
    @stack('styles')
</head>
<body>
    @auth
        @includeWhen(auth()->user()->isAdmin(), 'partials.nav-admin')
        @includeWhen(auth()->user()->isCommercial(), 'partials.nav-commercial')
    @endauth
    
    <main class="container py-4">
        {{-- Messages flash --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4">
                <i class="fas fa-check-circle me-3" style="color: var(--success-color);"></i>
                <div class="flex-grow-1">
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-3" style="color: var(--danger-color);"></i>
                    <div>
                        <strong>Erreur :</strong>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4">
                <i class="fas fa-exclamation-triangle me-3" style="color: var(--danger-color);"></i>
                <div class="flex-grow-1">
                    {{ session('error') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>
<!-- 
    <footer class="mt-auto">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} WaveOrders. Tous droits réservés.</p>
        </div>
    </footer>-->

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('scripts')
</body>
</html>