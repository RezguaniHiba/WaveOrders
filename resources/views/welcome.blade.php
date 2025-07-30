<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bienvenue sur Gestion Commandes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f1f3f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .welcome-box {
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .btn-primary {
            background-color: #1A237E;
            border: none;
        }

        .btn-primary:hover {
            background-color: #000;
        }

        .logo {
            width: 120px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="welcome-box">
    <img src="{{ asset('images/logo2.png') }}" alt="Logo" class="logo">
    <h2>Bienvenue sur l'application de gestion des commandes</h2>
    <p class="mt-3">Gérez facilement vos clients, commandes et produits avec intégration à WaveSoft.</p>
        <a href="{{ route('login') }}" class="btn btn-primary">Se connecter</a>
</div>

</body>
</html>
