?

<?php $__env->startSection('title', 'Mes Examens'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                        <i class="bi bi-calendar-check fs-2"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">Examens à Venir</h3>
                        <p class="mb-0 opacity-75">Consultez vos dates d'examens et le temps restant pour vous préparer.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php $__empty_1 = true; $__currentLoopData = $examens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $examen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge bg-<?php echo e($examen->countdown_class); ?> mb-2 px-3 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-hourglass-split me-1"></i> <?php echo e($examen->countdown); ?>

                        </span>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-light text-dark border px-2 py-1">
                            Coef: <?php echo e($examen->coefficient); ?>

                        </span>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <h5 class="card-title fw-bold text-primary mb-3"><?php echo e($examen->module->nom); ?></h5>
                    
                    <div class="d-flex align-items-center mb-2 text-muted">
                        <i class="bi bi-tag-fill me-2"></i>
                        <span>Type: <strong><?php echo e($examen->type); ?></strong></span>
                    </div>

                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-calendar3 me-2 text-secondary"></i>
                        <span class="fw-bold text-dark"><?php echo e($examen->date->format('l d F Y')); ?></span>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-clock me-2 text-secondary"></i>
                        <span><?php echo e(substr($examen->heure_debut, 0, 5)); ?> - <?php echo e(substr($examen->heure_fin, 0, 5)); ?></span>
                    </div>

                    <div class="bg-light rounded p-3 d-flex align-items-center">
                        <i class="bi bi-geo-alt-fill me-2 text-danger"></i>
                        <span class="small fw-bold"><?php echo e($examen->salle ? $examen->salle->nom : 'Salle non spécifiée'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm text-center p-5">
                <div class="card-body">
                    <i class="bi bi-journal-check text-muted mb-3" style="font-size: 3rem;"></i>
                    <h4>Aucun examen programmé</h4>
                    <p class="text-muted">Vous n'avez pas d'examens à venir pour le moment.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .transition {
        transition: all 0.3s ease;
    }
</style>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.etudiant', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/etudiant/examens/index.blade.php ENDPATH**/ ?>