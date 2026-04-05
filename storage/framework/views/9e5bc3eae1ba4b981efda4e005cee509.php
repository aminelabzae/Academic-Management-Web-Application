<?php $__env->startSection('title', 'Tableau de Bord'); ?>
<?php $__env->startSection('subtitle', 'Vue d\'ensemble du système'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Dashboard Cards */
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-secondary {
    border-left: 0.25rem solid #858796 !important;
}
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.text-xs {
    font-size: 0.7rem;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}

.font-weight-bold {
    font-weight: 700 !important;
}

.h5 {
    font-size: 1.25rem;
}
.mb-0 {
    margin-bottom: 0 !important;
}

.shadow {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.py-2 {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
}

.card-body {
    flex: 1 1 auto;
    padding: 1rem;
}

.row {
    --bs-gutter-x: 1.5rem;
    --bs-gutter-y: 0;
    display: flex;
    flex-wrap: wrap;
    margin-top: calc(-1 * var(--bs-gutter-y));
    margin-right: calc(-0.5 * var(--bs-gutter-x));
    margin-left: calc(-0.5 * var(--bs-gutter-x));
}

.row > * {
    flex-shrink: 0;
    width: 100%;
    max-width: 100%;
    padding-right: calc(var(--bs-gutter-x) * 0.5);
    padding-left: calc(var(--bs-gutter-x) * 0.5);
    margin-top: var(--bs-gutter-y);
}

.col-xl-3 {
    flex: 0 0 auto;
    width: 25%;
}

.col-xl-6 {
    flex: 0 0 auto;
    width: 50%;
}

.col-lg-12 {
    flex: 0 0 auto;
    width: 100%;
}

.mb-4 {
    margin-bottom: 1.5rem !important;
}

.h-100 {
    height: 100% !important;
}

.align-items-center {
    align-items: center !important;
}

.no-gutters {
    --bs-gutter-x: 0;
    --bs-gutter-y: 0;
}

.mr-2 {
    margin-right: 0.5rem !important;
}

.col-auto {
    flex: 0 0 auto;
    width: auto;
}

.fa-2x {
    font-size: 2em;
}

.text-uppercase {
    text-transform: uppercase !important;
}

.text-primary {
    color: #5a5c69 !important;
}

.text-success {
    color: #1cc88a !important;
}

.text-info {
    color: #36b9cc !important;
}

.text-warning {
    color: #f6c23e !important;
}

.text-secondary {
    color: #858796 !important;
}

.text-danger {
    color: #e74a3b !important;
}

.text-muted {
    color: #6c757d !important;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table {
    --bs-table-color: var(--bs-body-color);
    --bs-table-bg: transparent;
    --bs-table-border-color: var(--bs-border-color);
    --bs-table-accent-bg: transparent;
    --bs-table-striped-color: var(--bs-body-color);
    --bs-table-striped-bg: rgba(0, 0, 0, 0.05);
    --bs-table-active-color: var(--bs-body-color);
    --bs-table-active-bg: rgba(0, 0, 0, 0.1);
    --bs-table-hover-color: var(--bs-body-color);
    --bs-table-hover-bg: rgba(0, 0, 0, 0.075);
    width: 100%;
    margin-bottom: 1rem;
    color: var(--bs-table-color);
    vertical-align: top;
    border-color: var(--bs-table-border-color);
}

.table-bordered {
    --bs-table-border-color: #e3e6f0;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid var(--bs-table-border-color);
}

.table th {
    background-color: #f8f9fc;
    font-weight: 600;
    border-bottom: 2px solid #e3e6f0;
}

@media (max-width: 768px) {
    .col-xl-3 {
        width: 50%;
    }
    .col-xl-6 {
        width: 100%;
    }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <!-- Statistiques -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Filières
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['filieres']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-mortarboard-fill fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Groupes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['groupes']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people-fill fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Professeurs Actifs
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['professeurs']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-badge-fill fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Stagiaires Actifs
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['etudiants']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-fill fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Total Absences
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['absences']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-x-fill fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(count($demandesAnnulation) > 0): ?>
<!-- Demandes d'Annulation en Attente -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card border-left-warning shadow">
            <div class="card-header py-3 bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-warning"><i class="bi bi-exclamation-triangle-fill me-2"></i>Demandes d'Annulation en Attente</h6>
                <span class="badge bg-warning text-dark"><?php echo e(count($demandesAnnulation)); ?> demande(s)</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>Professeur</th>
                                <th>Module / Groupe</th>
                                <th>Date / Heure</th>
                                <th>Motif</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $demandesAnnulation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><strong><?php echo e($demande->professeur->nom_complet); ?></strong></td>
                                    <td><?php echo e($demande->module->nom); ?><br><small class="text-muted"><?php echo e($demande->groupe->nom); ?></small></td>
                                    <td><?php echo e($demande->jour); ?><br><small class="text-muted"><?php echo e($demande->heure_debut); ?> - <?php echo e($demande->heure_fin); ?></small></td>
                                    <td><em class="text-danger">"<?php echo e($demande->motif_annulation); ?>"</em></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <form action="<?php echo e(route('admin.emplois.approve', $demande->id)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-circle me-1"></i>Approuver
                                                </button>
                                            </form>
                                            <form action="<?php echo e(route('admin.emplois.reject', $demande->id)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-x-circle me-1"></i>Refuser
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Dernières Séances -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Dernières Séances Programmées</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Jour</th>
                                <th>Heure</th>
                                <th>Module</th>
                                <th>Professeur</th>
                                <th>Groupe</th>
                                <th>Salle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $dernieresSeances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($seance->jour); ?></td>
                                    <td><?php echo e($seance->heure_debut); ?> - <?php echo e($seance->heure_fin); ?></td>
                                    <td><?php echo e($seance->module->nom ?? 'N/A'); ?></td>
                                    <td><?php echo e($seance->professeur->nom_complet ?? 'N/A'); ?></td>
                                    <td><?php echo e($seance->groupe->nom ?? 'N/A'); ?></td>
                                    <td><?php echo e($seance->salle->nom ?? 'N/A'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Aucune séance trouvée</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>