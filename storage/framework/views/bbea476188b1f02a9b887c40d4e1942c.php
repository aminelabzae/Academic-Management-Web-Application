?

<?php $__env->startSection('title', 'Gestion du Syllabus'); ?>
<?php $__env->startSection('subtitle', 'Module: ' . $module->nom); ?>

<?php $__env->startSection('actions'); ?>
    <a href="<?php echo e(route('admin.modules.index')); ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i> Retour aux modules
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-ol me-2"></i>Syllabus du Module</h5>
                <span class="badge bg-info text-dark">Total: <?php echo e($items->sum('poids_pourcentage')); ?>%</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Titre</th>
                                <th>Poids</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($item->ordre); ?></td>
                                    <td>
                                        <div class="fw-bold"><?php echo e($item->titre); ?></div>
                                        <?php if($item->description): ?>
                                            <small class="text-muted"><?php echo e(Str::limit($item->description, 50)); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($item->poids_pourcentage); ?>%</td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4">Aucun chapitre défini pour ce module.</td>
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



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/modules/syllabus.blade.php ENDPATH**/ ?>