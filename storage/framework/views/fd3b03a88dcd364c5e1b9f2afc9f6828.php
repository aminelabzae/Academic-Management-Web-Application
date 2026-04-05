?

<?php $__env->startSection('title', $module->nom); ?>
<?php $__env->startSection('subtitle', 'Détails du module'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Informations</h5></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th>Code:</th><td><strong><?php echo e($module->code); ?></strong></td></tr>
                    <tr><th>Filières:</th><td><?php echo e($module->filieres->pluck('nom')->implode(', ')); ?></td></tr>
                    <tr><th>Semestre:</th><td><span class="badge bg-info">S<?php echo e($module->semestre); ?></span></td></tr>
                    <tr><th>Coefficient:</th><td><?php echo e($module->coefficient); ?></td></tr>
                    <?php
                        $heuresHebdo = $module->getHeuresHebdomadairesActuelles();
                        $heuresTotales = $module->getHeuresTotalesByGroupe();
                        $maxHeures = $module->masse_horaire;
                    ?>
                    <tr>
                        <th class="py-3"><i class="bi bi-clock me-1 text-primary"></i>Charge prévue:</th>
                        <td class="py-3">
                            <span class="badge bg-primary fs-6"><?php echo e(\App\Models\EmploiDuTemps::formatHeures($heuresHebdo)); ?> / mois</span>
                        </td>
                    </tr>
                    <tr class="border-bottom">
                        <th class="pb-3 text-muted"><i class="bi bi-calendar-check me-1"></i>Total consommé (Validé):</th>
                        <td class="pb-3 text-muted"><?php echo e(\App\Models\EmploiDuTemps::formatHeures($heuresTotales)); ?></td>
                    </tr>

                    <?php if($maxHeures > 0): ?>
                        <tr>
                            <th colspan="2" class="pt-4"><i class="bi bi-bar-chart-fill me-2"></i>Progression par groupe</th>
                        </tr>
                        <?php $__currentLoopData = $module->filieres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filiere): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $filiere->groupes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $heuresGroupePrevues = $module->getHeuresHebdomadairesByGroupe($groupe->id);
                                    $heuresGroupeConsommees = $module->getHeuresTotalesByGroupe($groupe->id);
                                    // Le pourcentage est maintenant basé sur la charge prévue (automatique)
                                    $pourcentage = $maxHeures > 0 ? round(($heuresGroupePrevues / $maxHeures) * 100) : 0;
                                ?>
                                <tr>
                                    <td colspan="2" class="pb-4">
                                        <div class="d-flex justify-content-between mb-1">
                                            <div>
                                                <small class="fw-bold">Groupe: <?php echo e($groupe->nom); ?></small>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-primary d-block fw-semibold"><?php echo e(\App\Models\EmploiDuTemps::formatHeures($heuresGroupePrevues)); ?> affectées / <?php echo e($maxHeures); ?> h</small>
                                                <small class="text-success" style="font-size: 0.75rem;">Total validé: <?php echo e(\App\Models\EmploiDuTemps::formatHeures($heuresGroupeConsommees)); ?></small>
                                            </div>
                                        </div>
                                        <div class="progress" style="height: 12px;">
                                            <div class="progress-bar <?php echo e($pourcentage >= 100 ? 'bg-danger' : ($pourcentage >= 80 ? 'bg-warning' : 'bg-success')); ?>"
                                                 role="progressbar" style="width: <?php echo e(min($pourcentage, 100)); ?>%">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <tr><th>Heures réalisées:</th><td><?php echo e(\App\Models\EmploiDuTemps::formatHeures($module->getHeuresTotalesByGroupe(0))); ?> (Global)</td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="<?php echo e(route('admin.modules.index')); ?>" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Retour</a>
    <a href="<?php echo e(route('admin.modules.edit', $module)); ?>" class="btn btn-warning"><i class="bi bi-pencil me-2"></i> Modifier</a>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/modules/show.blade.php ENDPATH**/ ?>