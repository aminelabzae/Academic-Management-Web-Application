?

<?php $__env->startSection('title', 'Gestion des Absences'); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-person-x me-2"></i>Dernières Absences & Retards</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date & Séance</th>
                        <th>Groupe / Module</th>
                        <th>Stagiaire</th>
                        <th class="text-center">Statut</th>
                        <th>Commentaire</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $absences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $absence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div><?php echo e(\Carbon\Carbon::parse($absence->seanceRealisation->date)->locale('fr')->isoFormat('DD MMMM YYYY')); ?></div>
                                <small class="text-muted"><?php echo e($absence->seanceRealisation->emploiDuTemps->heure_debut); ?> - <?php echo e($absence->seanceRealisation->emploiDuTemps->heure_fin); ?></small>
                            </td>
                            <td>
                                <div><?php echo e($absence->seanceRealisation->emploiDuTemps->groupe->nom); ?></div>
                                <small class="badge bg-light text-dark"><?php echo e($absence->seanceRealisation->emploiDuTemps->module->nom); ?></small>
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo e($absence->etudiant->nom_complet); ?></div>
                                <small class="text-muted"><?php echo e($absence->etudiant->cef); ?></small>
                            </td>
                            <td class="text-center">
                                <?php if($absence->status == 'Présent'): ?>
                                    <span class="badge bg-success">Présent</span>
                                <?php elseif($absence->status == 'Absent'): ?>
                                    <span class="badge bg-danger">Absent</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Justifié</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted"><?php echo e($absence->commentaire ?: '-'); ?></small>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">Aucune absence enregistrée.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($absences->hasPages()): ?>
        <div class="card-footer bg-white">
            <?php echo e($absences->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.professeur', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/professeur/absences.blade.php ENDPATH**/ ?>