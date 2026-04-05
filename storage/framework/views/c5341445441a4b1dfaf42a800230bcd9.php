?

<?php $__env->startSection('title', 'Historique des Séances'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="bi bi-clock-history me-2"></i>Historique des Séances Validées</h4>
    <a href="<?php echo e(route('professeur.dashboard')); ?>" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-house me-1"></i> Dashboard
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Module</th>
                                <th>Groupe</th>
                                <th>Horaire</th>
                                <th>Durée</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $totalMinutes = 0;
                            ?>
                            <?php $__empty_1 = true; $__currentLoopData = $realisations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $realisation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $emploi = $realisation->emploiDuTemps;
                                    $module = $realisation->module ?? $emploi->module;
                                    $debut = \Carbon\Carbon::parse($emploi->heure_debut);
                                    $fin = \Carbon\Carbon::parse($emploi->heure_fin);
                                    $minutes = $debut->diffInMinutes($fin);
                                    $totalMinutes += $minutes;
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo e(\Carbon\Carbon::parse($realisation->date)->format('d/m/Y')); ?></div>
                                        <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($realisation->date)->locale('fr')->isoFormat('dddd')); ?></small>
                                    </td>
                                    <td>
                                        <div><?php echo e($module->nom); ?></div>
                                        <small class="text-muted"><?php echo e($module->code); ?></small>
                                    </td>
                                    <td><?php echo e($emploi->groupe->nom); ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?php echo e($emploi->heure_debut); ?> - <?php echo e($emploi->heure_fin); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($minutes / 60); ?>h</td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-info-circle display-4 text-muted mb-3 d-block"></i>
                                        <p class="text-muted">Aucune séance validée pour le moment.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if($realisations->count() > 0): ?>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end text-uppercase small">Total cumulé :</td>
                                <td><?php echo e($totalMinutes / 60); ?>h</td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.professeur', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/professeur/seances_realisees.blade.php ENDPATH**/ ?>