<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .password-reset-card {
            max-width: 450px;
            width: 100%;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        .password-reset-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .password-reset-header h2 {
            color: #2c3e50;
            font-weight: 600;
        }
        .password-reset-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
        }
        .btn-reset {
            width: 100%;
            padding: 10px;
            font-weight: 500;
            background-color: #3490dc;
            border: none;
        }
        .btn-reset:hover {
            background-color: #2779bd;
        }
        .form-control:focus {
            border-color: #3490dc;
            box-shadow: 0 0 0 0.25rem rgba(52, 144, 220, 0.25);
        }
        .back-to-login {
            text-align: center;
            margin-top: 1.5rem;
        }
        .back-to-login a {
            color: #3490dc;
            text-decoration: none;
        }
        .back-to-login a:hover {
            color: #1d68a7;
            text-decoration: underline;
        }
        .back-to-login a:active {
            color: #15507c;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="password-reset-card">
        <div class="password-reset-header">
            <!-- Ajoutez votre logo ici si vous en avez un -->
            <!-- <img src="{{ asset('images/logo.png') }}" alt="Logo" class="password-reset-logo"> -->
            <h2>Créer un nouveau mot de passe</h2>
            <p class="text-muted">Entrez votre nouveau mot de passe</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label for="email" class="form-label">Adresse email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ $email ?? old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Nouveau mot de passe</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       id="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password-confirm" class="form-label">Confirmer le mot de passe</label>
                <input type="password" class="form-control" 
                       id="password-confirm" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-primary btn-reset mt-3">
                Réinitialiser le mot de passe
            </button>
        </form>

        <div class="back-to-login">
            <a href="{{ route('login') }}">
                <i class="fas fa-arrow-left"></i> Retour à la connexion
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Optionnel : Font Awesome pour les icônes -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>