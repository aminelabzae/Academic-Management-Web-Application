

<?php $__env->startSection('title', 'Mon Tableau de Bord'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-success text-white shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4><i class="bi bi-mortarboard me-2"></i>Bienvenue, <?php echo e(auth()->user()->name); ?></h4>
                        <?php if($etudiant && $etudiant->groupe): ?>
                            <p class="mb-0">
                                Groupe: <strong><?php echo e($etudiant->groupe->nom); ?></strong> |
                                Filière: <strong><?php echo e($etudiant->groupe->filiere->nom); ?></strong>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(count($notifications) > 0): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 text-danger"><i class="bi bi-bell-fill me-2"></i>Dernières Notifications</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-light">
                            <div>
                                <i class="bi bi-info-circle-fill text-primary me-2"></i>
                                <?php echo $notif->data['message'] ?? 'Notification'; ?>

                                <br><small class="text-muted"><?php echo e($notif->created_at->diffForHumans()); ?></small>
                            </div>
                            <form action="<?php echo e(route('notifications.read', $notif->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-link text-secondary p-0" title="Marquer comme lu">
                                    <i class="bi bi-check-all fs-4"></i>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $unreadAnnonces = $annonces->filter(fn($a) => !$a->isReadBy(auth()->user())); ?>
<?php if($unreadAnnonces->count() > 0): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 text-primary"><i class="bi bi-megaphone-fill me-2"></i>Annonces</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php $__currentLoopData = $unreadAnnonces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $annonce): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-group-item bg-light">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold"><i class="bi bi-pin-angle-fill text-warning me-2"></i><?php echo e($annonce->titre); ?></h6>
                                    <p class="mb-1 text-muted"><?php echo nl2br(e($annonce->contenu)); ?></p>
                                </div>
                                <div class="d-flex align-items-center ms-3">
                                    <small class="text-muted text-nowrap me-2"><?php echo e($annonce->created_at->diffForHumans()); ?></small>
                                    <form action="<?php echo e(route('etudiant.annonces.read', $annonce->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-link text-secondary p-0" title="Marquer comme vu">
                                            <i class="bi bi-check-all fs-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 text-success"><i class="bi bi-calendar-day me-2"></i>Mes cours d'aujourd'hui</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr><th>Horaire</th><th>Module</th><th>Professeur</th><th>Salle</th></tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $seancesAujourdhui; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="<?php echo e($seance->is_examen ? 'table-danger' : ''); ?>">
                            <td>
                                <span class="badge <?php echo e($seance->is_examen ? 'bg-danger' : 'bg-success'); ?>">
                                    <?php echo e($seance->heure_debut); ?> - <?php echo e($seance->heure_fin); ?>

                                </span>
                            </td>
                            <td>
                                <strong><?php echo e($seance->module->nom); ?></strong>
                                <?php if($seance->is_examen): ?>
                                    <span class="badge bg-danger ms-2">EXAMEN</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($seance->professeur->nom_complet); ?></td>
                            <td>
                                <?php if($seance->type_seance === 'Teams'): ?>
                                    <span class="badge bg-info p-1"><i class="bi bi-laptop me-1"></i>Teams</span>
                                <?php else: ?>
                                    <?php echo e($seance->salle->nom); ?>

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-center text-muted py-5">Aucun cours aujourd'hui</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        <a href="<?php echo e(route('etudiant.emploi')); ?>" class="btn btn-success px-4">
            <i class="bi bi-calendar3 me-2"></i> Voir mon emploi du temps complet
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.etudiant', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/etudiant/dashboard.blade.php ENDPATH**/ ?>