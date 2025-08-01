<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
                {{ file_get_contents(public_path('css/custom-login.css')) }}
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row card0 m-0">
        <!-- Colonne gauche -->
        <div class="col-lg-6 col-md-6 col-sm-12 card1">
            <img src="{{ asset('images/logo4.jpg') }}" alt="Logo" class="logo">
            <img src="{{ asset('images/gestord.png') }}" alt="Image illustrative" class="image">
        </div>

        <!-- Colonne droite -->
        <div class="col-lg-6 col-md-6 col-sm-12 p-0">
            <div class="card2">
                <h4 class="mb-4 text-center">Connexion</h4>

                @if ($errors->any())
                    <div class="alert alert-danger text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group px-3">
                        <label><h6 class="mb-0 text-sm">Adresse email</h6></label>
                        <input class="form-control mb-3" type="email" name="email" placeholder="Entrez votre adresse email" required>
                    </div>

                    <div class="form-group px-3">
                        <label><h6 class="mb-0 text-sm">Mot de passe</h6></label>
                        <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
                    </div>

                    <div class="form-group px-3 mb-4 d-flex justify-content-between">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                            <label class="custom-control-label text-sm" for="remember">Se souvenir de moi</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-sm">Mot de passe oublié ?</a>
                    </div>

                    <div class="row mb-3 px-3">
                        <button type="submit" class="btn btn-blue">Se connecter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>

</body>
</html>
