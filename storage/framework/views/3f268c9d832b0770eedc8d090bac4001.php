?

<?php $__env->startSection('title', 'Gestion des Examens'); ?>
<?php $__env->startSection('subtitle', 'Liste des examens programmés'); ?>

<?php $__env->startSection('actions'); ?>
    <a href="<?php echo e(route('admin.examens.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Nouvel Examen
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date & Heure</th>
                        <th>Groupe</th>
                        <th>Module</th>
                        <th>Type</th>
                        <th>Coefficient</th>
                        <th>Salle</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $examens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $examen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?php echo e($examen->date->format('d/m/Y')); ?></div>
                                <small class="text-muted"><?php echo e(substr($examen->heure_debut, 0, 5)); ?> - <?php echo e(substr($examen->heure_fin, 0, 5)); ?></small>
                            </td>
                            <td><?php echo e($examen->groupe->nom); ?></td>
                            <td><?php echo e($examen->module->nom); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($examen->type == 'EFF' ? 'danger' : ($examen->type == 'EFM Régional' ? 'warning' : 'primary')); ?>">
                                    <?php echo e($examen->type); ?>

                                </span>
                            </td>
                            <td><span class="badge bg-secondary">x<?php echo e($examen->coefficient); ?></span></td>
                            <td><?php echo e($examen->salle ? $examen->salle->nom : 'N/A'); ?></td>
                            <td>
                                <a href="<?php echo e(route('admin.examens.edit', $examen)); ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                <form action="<?php echo e(route('admin.examens.destroy', $examen)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet examen ?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Aucun examen trouvé</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/examens/index.blade.php ENDPATH**/ ?>