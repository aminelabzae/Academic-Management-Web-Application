<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Espace Professeur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('OFPPT_Logo.png') }}">
    <style>
        :root {
            --ofppt-blue: #003366;
            --ofppt-accent: #2e86de;
        }
        body {
            background-color: #f8f9fa;
        }
        .navbar-custom {
            background: linear-gradient(90deg, var(--ofppt-blue) 0%, #004080 100%);
        }
        .navbar-custom .navbar-brand {
            color: white;
            font-weight: 700;
        }
        .navbar-custom .nav-link {
            color: rgba(255,255,255,0.9);
        }
        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: #ffc107; /* Minimal highlight */
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .table-responsive {
            -webkit-overflow-scrolling: touch;
        }
        @media (max-width: 768px) {
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }
            .btn-sm {
                padding: 0.4rem 0.6rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('professeur.dashboard') }}">
                <img src="{{ asset('OFPPT_Logo.png') }}" alt="OFPPT Logo" class="me-2" style="height: 35px; width: auto; object-fit: contain;">
                <span>Espace Professeur</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professeur.dashboard') ? 'active' : '' }}" href="{{ route('professeur.dashboard') }}">
                            <i class="bi bi-house me-1"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professeur.emploi') ? 'active' : '' }}" href="{{ route('professeur.emploi') }}">
                            <i class="bi bi-calendar3 me-1"></i> Mon Emploi du Temps
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professeur.seances.realisees') ? 'active' : '' }}" href="{{ route('professeur.seances.realisees') }}">
                            <i class="bi bi-clock-history me-1"></i> Historique des Séances
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professeur.absences') ? 'active' : '' }}" href="{{ route('professeur.absences') }}">
                            <i class="bi bi-person-x me-1"></i> Absences
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professeur.avancement') ? 'active' : '' }}" href="{{ route('professeur.avancement') }}">
                            <i class="bi bi-graph-up-arrow me-1"></i> Mon Avancement
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center">
                    <!-- Notifications Dropdown -->
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link dropdown-toggle hide-arrow p-0 position-relative text-white" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-bell-fill fs-5"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="width: 320px; max-height: 400px; overflow-y: auto;">
                            <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Notifications</h6>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size: 0.75rem;">Tout marquer lu</button>
                                    </form>
                                @endif
                            </li>
                            @forelse(auth()->user()->notifications->take(10) as $notification)
                                <li class="px-3 py-2 border-bottom {{ $notification->unread() ? 'bg-light' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="pe-2">
                                            <div class="small fw-bold">{{ $notification->data['message'] ?? 'Notification' }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                        @if($notification->unread())
                                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-link text-secondary p-0" title="Marquer comme lu">
                                                    <i class="bi bi-check-all fs-5"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="px-3 py-4 text-center text-muted">
                                    <i class="bi bi-bell-slash fs-4 d-block mb-2"></i>
                                    Aucune notification
                                </li>
                            @endforelse
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


