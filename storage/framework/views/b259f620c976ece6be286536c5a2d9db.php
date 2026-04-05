<?php $__env->startSection('title', 'Archives Mensuelles'); ?>
<?php $__env->startSection('subtitle', 'Historique des mois passés'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <?php $__empty_1 = true; $__currentLoopData = $realisations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $date = \Carbon\Carbon::create($v->year, $v->month, 1);
        ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="bi bi-calendar-range me-2"></i><?php echo e(ucfirst($date->translatedFormat('F'))); ?>

                        </h5>
                        <span class="badge bg-secondary"><?php echo e($v->year); ?></span>
                    </div>
                    
                    <p class="text-muted small mb-3">
                        Mois complet (<?php echo e($v->total_sessions); ?> séances réalisées)
                    </p>

                    <div class="row text-center bg-light rounded py-3 mb-3 mx-0">
                        <div class="col-6 border-end">
                            <h4 class="mb-0 text-dark"><?php echo e($v->total_sessions); ?></h4>
                            <small class="text-muted text-uppercase" style="font-size: 0.7rem;">Séances</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-0 text-dark"><?php echo e(round($v->total_minutes / 60, 1)); ?>h</h4>
                            <small class="text-muted text-uppercase" style="font-size: 0.7rem;">Total Heures</small>
                        </div>
                    </div>

                    <a href="<?php echo e(route('admin.archives.show', ['year' => $v->year, 'month' => $v->month])); ?>" class="btn btn-outline-primary w-100">
                        <i class="bi bi-eye me-2"></i>Consulter le rapport
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12 text-center py-5">
            <div class="card py-5">
                <i class="bi bi-archive text-muted mb-3" style="font-size: 3rem;"></i>
                <h5>Aucune archive disponible</h5>
                <p class="text-muted">Les archives seront créées automatiquement au fur et à mesure que les professeurs valident leurs séances.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="d-flex justify-content-center mt-4">
    <?php echo e($realisations->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\aminelabzae\OneDrive\Documents\Programmation\2em-annee\laravel\emploi-du-temps (8)\emploi-du-temps\resources\views/admin/archives/index.blade.php ENDPATH**/ ?>