?

<?php $__env->startSection('title', 'Détails de la Séance'); ?>
<?php $__env->startSection('subtitle', $emploi->jour . ' ' . $emploi->heure_debut . ' - ' . $emploi->heure_fin); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Informations de la séance</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><th>Jour:</th><td><span class="badge bg-primary fs-6"><?php echo e($emploi->jour); ?></span></td></tr>
                            <tr><th>Horaire:</th><td><?php echo e($emploi->heure_debut); ?> - <?php echo e($emploi->heure_fin); ?></td></tr>
                            <tr><th>Groupe:</th><td><?php echo e($emploi->groupe->nom); ?></td></tr>
                            <tr><th>Filière:</th><td><?php echo e($emploi->groupe->filiere->nom); ?></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><th>Module:</th><td><?php echo e($emploi->module->nom); ?></td></tr>
                            <tr><th>Professeur:</th><td><?php echo e($emploi->professeur->nom_complet); ?></td></tr>
                            <tr>
                                <th>Salle:</th>
                                <td>
                                    <?php if($emploi->type_seance === 'Teams'): ?>
                                        <span class="badge bg-info">Distance (Teams)</span>
                                        <?php if($emploi->teams_link): ?>
                                            <div class="mt-2">
                                                <a href="<?php echo e($emploi->teams_link); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-link-45deg"></i> Ouvrir le lien
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <?php elseif($emploi->salle): ?>
                                        <?php echo e($emploi->salle->nom); ?> (<?php echo e($emploi->salle->type); ?>)
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr><th>Semaine:</th><td><?php echo e($emploi->semaine_type); ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 text-center">
    <a href="<?php echo e(route('admin.emplois.index')); ?>" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Retour</a>
    <a href="<?php echo e(route('admin.emplois.edit', $emploi)); ?>" class="btn btn-warning"><i class="bi bi-pencil me-2"></i> Modifier</a>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/emplois/show.blade.php ENDPATH**/ ?>