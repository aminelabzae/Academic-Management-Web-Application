<?php $__env->startSection('title', 'Gestion des Salles'); ?>

<?php $__env->startSection('actions'); ?>
    <a href="<?php echo e(route('admin.salles.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Nouvelle salle
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Capacité</th>
                        <th>Bâtiment</th>
                        <th>Statut</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $salles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($salle->numero); ?></strong></td>
                            <td><?php echo e($salle->nom); ?></td>
                            <td><span class="badge bg-info"><?php echo e($salle->type); ?></span></td>
                            <td><?php echo e($salle->capacite); ?> places</td>
                            <td><?php echo e($salle->batiment ?? '-'); ?></td>
                             <td>
                                <form action="<?php echo e(route('admin.salles.toggle', $salle)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm <?php echo e($salle->disponible ? 'btn-success' : 'btn-danger'); ?>" title="Cliquer pour changer le statut">
                                        <?php echo e($salle->disponible ? 'Disponible' : 'Indisponible'); ?>

                                    </button>
                                </form>
                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.salles.show', $salle)); ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                <a href="<?php echo e(route('admin.salles.edit', $salle)); ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                <form action="<?php echo e(route('admin.salles.destroy', $salle)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette salle ?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Aucune salle trouvée</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($salles->hasPages()): ?>
        <div class="card-footer px-4 py-3">
            <?php echo e($salles->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\aminelabzae\OneDrive\Documents\Programmation\2em-annee\laravel\emploi-du-temps (8)\emploi-du-temps\resources\views/admin/salles/index.blade.php ENDPATH**/ ?>