<?php $__env->startSection('title', $filiere->nom); ?>
<?php $__env->startSection('subtitle', 'Détails de la filière'); ?>

<?php $__env->startSection('actions'); ?>
    <a href="<?php echo e(route('admin.filieres.edit', $filiere)); ?>" class="btn btn-warning">
        <i class="bi bi-pencil me-2"></i> Modifier
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informations</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Code:</th>
                        <td><strong><?php echo e($filiere->code); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Niveau:</th>
                        <td><span class="badge bg-info"><?php echo e($filiere->niveau); ?></span></td>
                    </tr>
                    <tr>
                        <th>Durée:</th>
                        <td><?php echo e($filiere->duree_formation); ?> ans</td>
                    </tr>
                    <tr>
                        <th>Statut:</th>
                        <td>
                            <?php if($filiere->active): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                <?php if($filiere->description): ?>
                    <hr>
                    <p class="text-muted mb-0"><?php echo e($filiere->description); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Groupes (<?php echo e($filiere->groupes->count()); ?>)</h5>
                <a href="<?php echo e(route('admin.groupes.create')); ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Ajouter
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Année</th>
                                <th>Effectif</th>
                                <th>Année scolaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $filiere->groupes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><a href="<?php echo e(route('admin.groupes.show', $groupe)); ?>"><?php echo e($groupe->nom); ?></a></td>
                                    <td><?php echo e($groupe->annee); ?>ère année</td>
                                    <td><?php echo e($groupe->effectif); ?> stagiaires</td>
                                    <td><?php echo e($groupe->annee_scolaire); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Aucun groupe</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Modules (<?php echo e($filiere->modules->count()); ?>)</h5>
                <a href="<?php echo e(route('admin.modules.create')); ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Ajouter
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Nom</th>
                                <th>Semestre</th>
                                <th>Masse horaire</th>
                                <th>Coefficient</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $filiere->modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($module->code); ?></strong></td>
                                    <td><?php echo e($module->nom); ?></td>
                                    <td><span class="badge bg-secondary">S<?php echo e($module->semestre); ?></span></td>
                                    <td><?php echo e($module->masse_horaire); ?>h</td>
                                    <td><?php echo e($module->coefficient); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Aucun module</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="<?php echo e(route('admin.filieres.index')); ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i> Retour à la liste
    </a>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/filieres/show.blade.php ENDPATH**/ ?>