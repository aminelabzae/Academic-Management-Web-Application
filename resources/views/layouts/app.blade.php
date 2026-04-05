<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Gestion Emploi du Temps')</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('OFPPT_Logo.png') }}">
</head>
<body>

<!-- TOP NAVBAR -->
<header class="navbar">
    <div class="navbar-left" style="display: flex; align-items: center;">
        <img src="{{ asset('OFPPT_Logo.png') }}" alt="OFPPT Logo" style="height: 30px; margin-right: 10px;">
        <strong>Emploi du Temps</strong>
    </div>

    <div class="navbar-right">
        @auth
            <span>{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>

            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="logout-btn">Déconnexion</button>
            </form>
        @endauth
    </div>
</header>

<!-- PAGE CONTENT -->
<div class="page-container">
    @yield('content')
</div>

</body>
</html>


