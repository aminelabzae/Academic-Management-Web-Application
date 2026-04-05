<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="<?php echo e(asset('OFPPT_Logo.png')); ?>">
    <title><?php echo $__env->yieldContent('title'); ?> - ISTAM Gestion EDT</title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --ofppt-blue: #003366;
            --ofppt-accent: #2e86de; /* Changed from orange to a different blue for accent */
        }
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            overflow-y: auto;
            background: linear-gradient(180deg, var(--ofppt-blue) 0%, #001a33 100%);
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            z-index: 1050;
            transition: all 0.3s ease;
        }

        /* Custom Scrollbar for Sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 10px;
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.15); /* Changed from orange background */
            border-left: 4px solid var(--ofppt-accent);
        }
        .sidebar .nav-link i {
            width: 25px;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s ease;
        }
        .top-navbar {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 15px 25px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
        }
        .btn-primary {
            background: var(--ofppt-blue);
            border-color: var(--ofppt-blue);
        }
        .btn-primary:hover {
            background: #002244;
            border-color: #002244;
        }
        .btn-warning {
            background: #28a745; /* Changed from orange to green */
            border-color: #28a745;
            color: white;
        }
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        .badge-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        .logo-section {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 10px;
        }
        .logo-section h4 {
            color: white; /* Changed from orange to white */
            font-weight: 700;
            margin: 0;
        }
        .logo-section small {
            color: rgba(255,255,255,0.6);
            font-size: 11px;
        }
        
        /* Modern Pagination Styles */
        .card-footer {
            background-color: #fff;
            border-top: 1px solid #eee;
            padding: 1rem 1.5rem;
        }
        .pagination {
            margin-bottom: 0;
            gap: 5px;
        }
        .page-link {
            color: var(--ofppt-blue);
            border: 1px solid #e9ecef;
            border-radius: 6px !important;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .page-link:hover {
            background-color: #f0f7ff;
            color: var(--ofppt-blue);
            border-color: #cbdcf0;
        }
        .page-item.active .page-link {
            background-color: var(--ofppt-blue);
            border-color: var(--ofppt-blue);
            box-shadow: 0 4px 10px rgba(0, 51, 102, 0.2);
        }
        .page-item.disabled .page-link {
            background-color: #f8f9fa;
            border-color: #eee;
        }
        
        nav.flex.items.center.justify-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .pagination-info {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .sidebar-toggler {
            display: none;
            background: var(--ofppt-blue);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            margin-right: 15px;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }

        @media (max-width: 992px) {
            .sidebar {
                left: -250px;
                width: 250px;
            }
            .sidebar.active {
                left: 0;
            }
            .main-content {
                margin-left: 0;
                padding: 10px;
            }
            .sidebar-toggler {
                display: block;
                font-size: 1.5rem;
                padding: 4px 10px;
            }
            .sidebar-overlay.active {
                display: block;
            }
            .top-navbar {
                padding: 10px 15px;
                flex-direction: row;
                gap: 5px;
                margin-bottom: 15px;
                flex-wrap: wrap;
            }
            .header-actions {
                width: 100%;
                order: 3;
                margin-top: 10px;
                display: flex;
                justify-content: center;
                gap: 5px;
            }
            .header-actions .btn span {
                display: none;
            }
            .header-actions .btn i {
                margin-right: 0 !important;
            }
            .top-navbar h4 {
                font-size: 1.1rem;
            }
            .top-navbar small {
                display: none;
            }
            .user-name-text {
                display: none;
            }
            .table th, .table td {
                font-size: 0.8rem;
                padding: 0.5rem 0.25rem;
            }
            .btn-sm {
                padding: 0.25rem 0.4rem;
                font-size: 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .top-navbar h4 {
                max-width: 150px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="logo-section">
                <img src="<?php echo e(asset('OFPPT_Logo.png')); ?>" alt="Logo" class="img-fluid mb-2" style="max-height: 50px;">
                <small class="d-block">Gestion des Emplois du Temps</small>
            </div>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>">
                        <i class="bi bi-speedometer2"></i> Tableau de bord
                    </a>
                </li>

                <li class="nav-item mt-3">
                    <small class="text-white-50 px-3">GESTION</small>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.filieres.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.filieres.index')); ?>">
                        <i class="bi bi-book"></i> Filières
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.groupes.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.groupes.index')); ?>">
                        <i class="bi bi-people"></i> Groupes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.professeurs.index') ? 'active' : ''); ?>" href="<?php echo e(route('admin.professeurs.index')); ?>">
                        <i class="bi bi-person-badge"></i> Professeurs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.professeurs.paie') ? 'active' : ''); ?>" href="<?php echo e(route('admin.professeurs.paie')); ?>">
                        <i class="bi bi-file-earmark-text"></i> Rapports Professeurs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.absences.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.absences.index')); ?>">
                        <i class="bi bi-person-x"></i> Absences & Présences
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.etudiants.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.etudiants.index')); ?>">
                        <i class="bi bi-mortarboard"></i> Stagiaires
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.users.index')); ?>">
                        <i class="bi bi-people-fill"></i> Utilisateurs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.modules.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.modules.index')); ?>">
                        <i class="bi bi-journal-text"></i> Modules
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.salles.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.salles.index')); ?>">
                        <i class="bi bi-building"></i> Salles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.examens.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.examens.index')); ?>">
                        <i class="bi bi-calendar-check"></i> Examens
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.annonces.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.annonces.index')); ?>">
                        <i class="bi bi-megaphone"></i> Annonces
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.archives.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.archives.index')); ?>">
                        <i class="bi bi-archive"></i> Archives Mensuelles
                    </a>
                </li>

                <li class="nav-item mt-3">
                    <small class="text-white-50 px-3">EMPLOI DU TEMPS</small>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.emplois.index') ? 'active' : ''); ?>" href="<?php echo e(route('admin.emplois.index')); ?>">
                        <i class="bi bi-calendar3"></i> Liste des séances
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.emplois.grille') ? 'active' : ''); ?>" href="<?php echo e(route('admin.emplois.grille')); ?>">
                        <i class="bi bi-grid-3x3"></i> Grille par groupe
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.emplois.grille-semaine') ? 'active' : ''); ?>" href="<?php echo e(route('admin.emplois.grille-semaine')); ?>">
                        <i class="bi bi-calendar-week"></i> Grille semaine
                    </a>
                </li>

                <li class="nav-item mt-4">
                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="nav-link text-danger border-0 bg-transparent w-100 text-start">
                            <i class="bi bi-box-arrow-right"></i> Déconnexion
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content flex-grow-1">
            <div class="top-navbar d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="sidebar-toggler" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <h4 class="mb-0"><?php echo $__env->yieldContent('title'); ?></h4>
                        <small class="text-muted"><?php echo $__env->yieldContent('subtitle'); ?></small>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <!-- Notifications Dropdown -->
                    <div class="dropdown me-3">
                        <a class="text-muted position-relative" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-bell-fill fs-5"></i>
                            <?php if(auth()->user()->unreadNotifications->count() > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    <?php echo e(auth()->user()->unreadNotifications->count()); ?>

                                </span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="width: 320px; max-height: 400px; overflow-y: auto;">
                            <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Notifications</h6>
                                <?php if(auth()->user()->unreadNotifications->count() > 0): ?>
                                    <form action="<?php echo e(route('notifications.mark-all-read')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size: 0.75rem;">Tout marquer lu</button>
                                    </form>
                                <?php endif; ?>
                            </li>
                            <?php $__empty_1 = true; $__currentLoopData = auth()->user()->notifications->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <li class="px-3 py-2 border-bottom <?php echo e($notification->unread() ? 'bg-light' : ''); ?>">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="pe-2">
                                            <div class="small fw-bold"><?php echo e($notification->data['message'] ?? 'Notification'); ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;"><?php echo e($notification->created_at->diffForHumans()); ?></div>
                                        </div>
                                        <?php if($notification->unread()): ?>
                                            <form action="<?php echo e(route('notifications.read', $notification->id)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-link text-secondary p-0" title="Marquer comme lu">
                                                    <i class="bi bi-check-all fs-5"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <li class="px-3 py-4 text-center text-muted">
                                    <i class="bi bi-bell-slash fs-4 d-block mb-2"></i>
                                    Aucune notification
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <span class="me-3 user-name-text">
                        <i class="bi bi-person-circle me-1"></i>
                        <?php echo e(auth()->user()->name); ?>

                    </span>
                    <div class="header-actions">
                        <?php echo $__env->yieldContent('actions'); ?>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Erreurs :</strong>
                    <ul class="mb-0 mt-2">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                });
            }
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>


<?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/layouts/admin.blade.php ENDPATH**/ ?>