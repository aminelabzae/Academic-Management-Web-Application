

<?php $__env->startSection('title', $groupe->nom); ?>
<?php $__env->startSection('subtitle', 'Détails du groupe'); ?>

<?php $__env->startSection('actions'); ?>
    <a href__="<?php echo e(route('admin.emplois.grille', ['groupe_id' => $groupe->id])); ?>" class="btn btn-info me-2">
        <i class="bi bi-calendar3 me-2"></i> Voir EDT
    </a>
    <a href="<?php echo e(route('admin.groupes.edit', $groupe)); ?>" class="btn btn-warning">
        <i class="bi bi-pencil me-2"></i> Modifier
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informations</h5></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th>Filière:</th><td><?php echo e($groupe->filiere->nom); ?></td></tr>
                    <tr><th>Année:</th><td><?php echo e($groupe->annee); ?>ère année</td></tr>
                    <tr><th>Effectif:</th><td><?php echo e($groupe->effectif); ?> stagiaires</td></tr>
                    <tr><th>Année scolaire:</th><td><?php echo e($groupe->annee_scolaire); ?></td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Stagiaires (<?php echo e($groupe->etudiants->count()); ?>)</h5>
                <a href="<?php echo e(route('admin.etudiants.create')); ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Ajouter
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th>CEF</th><th>Nom</th><th>Prénom</th><th>Email</th></tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $groupe->etudiants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $etudiant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($etudiant->cef); ?></td>
                                    <td><?php echo e($etudiant->nom); ?></td>
                                    <td><?php echo e($etudiant->prenom); ?></td>
                                    <td><?php echo e($etudiant->email ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Aucun stagiaire</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="<?php echo e(route('admin.groupes.index')); ?>" class="btn btn-secondary">
    <i class="bi bi-arrow-left me-2"></i> Retour
</a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/groupes/show.blade.php ENDPATH**/ ?>