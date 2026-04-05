<?php $__env->startSection('title', 'Gestion des Séances'); ?>
<?php $__env->startSection('subtitle', 'Liste de toutes les séances'); ?>

<?php $__env->startSection('actions'); ?>

    <form action="<?php echo e(route('admin.emplois.generate')); ?>" method="POST" class="d-inline me-2">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-dark" title="Générer les instances de séances pour cette semaine">
            <i class="bi bi-calendar-plus me-2"></i> Initialiser la semaine
        </button>
    </form>
    <a href="<?php echo e(route('admin.emplois.grille')); ?>" class="btn btn-info me-2">
        <i class="bi bi-grid-3x3 me-2"></i> Voir la grille
    </a>
    <a href="<?php echo e(route('admin.emplois.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Nouvelle séance
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="d-flex">
            <input type="hidden" name="view" value="<?php echo e(request('view')); ?>">
            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher par jour, groupe, module ou professeur..." value="<?php echo e(request('search')); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Rechercher
            </button>
            <?php if(request('search')): ?>
                <a href="<?php echo e(route('admin.emplois.index', ['view' => request('view')])); ?>" class="btn btn-secondary ms-2">
                    <i class="bi bi-x-circle"></i> Effacer
                </a>
            <?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Jour</th>
                        <th>Horaire</th>
                        <th>Groupe</th>
                        <th>Module</th>
                        <th>Professeur</th>
                        <th>Salle</th>
                        <th>Statut</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $emplois; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emploi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><span class="badge bg-primary"><?php echo e($emploi->jour); ?></span></td>
                            <td><?php echo e($emploi->heure_debut); ?> - <?php echo e($emploi->heure_fin); ?></td>
                            <td><?php echo e($emploi->groupe->nom); ?></td>
                            <td><?php echo e($emploi->module->nom); ?></td>
                            <td><?php echo e($emploi->professeur->nom_complet); ?></td>
                            <td>
                                <?php if($emploi->type_seance === 'Teams'): ?>
                                    <span class="badge bg-info">Teams</span>
                                <?php else: ?>
                                    <?php echo e($emploi->salle->nom); ?>

                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($emploi->actif): ?><span class="badge bg-success">Actif</span>
                                <?php else: ?><span class="badge bg-secondary">Inactif</span><?php endif; ?>
                            </td>
                            <td>
                                <?php if(request('view') === 'trashed'): ?>
                                    <span class="badge bg-danger">Supprimé le <?php echo e($emploi->deleted_at->format('d/m/Y')); ?></span>
                                <?php else: ?>
                                    <a href="<?php echo e(route('admin.emplois.show', $emploi)); ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                    <a href="<?php echo e(route('admin.emplois.edit', $emploi)); ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                    <form action="<?php echo e(route('admin.emplois.destroy', $emploi)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Confirmer la suppression (elle sera archivée) ?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Aucune séance trouvée</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($emplois->hasPages()): ?><div class="card-footer"><?php echo e($emplois->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/emplois/index.blade.php ENDPATH**/ ?>