

<?php $__env->startSection('title', 'Mon Emploi du Temps'); ?>

<?php $__env->startSection('content'); ?>
<?php if($etudiant && $groupe): ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Emploi du temps - <?php echo e($groupe->nom); ?></h5>
        <span class="badge bg-success"><?php echo e($groupe->filiere->nom); ?></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-success">
                    <tr>
                        <th width="100">Horaire</th>
                        <?php $__currentLoopData = $jours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th class="text-center"><?php echo e($jour); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $creneaux; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $heureDebut => $heureFin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="align-middle text-center bg-light">
                                <strong><?php echo e($heureDebut); ?></strong><br>
                                <small class="text-muted"><?php echo e($heureFin); ?></small>
                            </td>
                            <?php $__currentLoopData = $jours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $seance = isset($emplois[$jour])
                                        ? $emplois[$jour]->first(function($s) use ($heureDebut) {
                                            return substr($s->heure_debut, 0, 5) == $heureDebut;
                                        })
                                        : null;
                                ?>
                                <td class="p-2 <?php echo e($seance ? ($seance->is_examen ? 'bg-danger bg-opacity-10' : 'bg-info bg-opacity-10') : ''); ?>">
                                    <?php if($seance): ?>
                                        <div class="p-2 rounded bg-white shadow-sm border-start border-4 <?php echo e($seance->is_examen ? 'border-danger' : 'border-info'); ?>">
                                            <?php if($seance->is_examen): ?>
                                                <span class="badge bg-danger mb-1">EXAMEN</span>
                                            <?php endif; ?>
                                            <strong class="d-block text-primary"><?php echo e($seance->module->nom); ?></strong>
                                            <small class="d-block text-muted">
                                                <i class="bi bi-person me-1"></i><?php echo e($seance->professeur->nom_complet); ?>

                                            </small>
                                            <small class="d-block text-muted">
                                                <?php if($seance->type_seance === 'Teams'): ?>
                                                    <span class="badge bg-info p-1 mb-1"><i class="bi bi-laptop me-1"></i>Teams</span>
                                                    <?php if($seance->teams_link): ?>
                                                        <a href="<?php echo e($seance->teams_link); ?>" target="_blank" class="btn btn-sm btn-primary d-block mt-2 py-0" style="font-size: 0.7rem;">
                                                            <i class="bi bi-camera-video me-1"></i>Rejoindre
                                                        </a>
                                                    <?php else: ?>
                                                        <small class="d-block text-muted mt-1" style="font-size: 0.6rem;">Lien non disponible</small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <i class="bi bi-building me-1"></i><?php echo e($seance->salle->nom); ?>

                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Votre profil stagiaire n'est pas encore configuré. Veuillez contacter l'administrateur.
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.etudiant', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/etudiant/emploi.blade.php ENDPATH**/ ?>