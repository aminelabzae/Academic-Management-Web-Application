

<?php $__env->startSection('title', 'Rapports Professeurs'); ?>
<?php $__env->startSection('subtitle', 'Suivi des validations, annulations et absences'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <!-- Demandes d'annulation -->
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm border-warning">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Demandes d'Annulation en Attente</h5>
                <span class="badge bg-dark"><?php echo e($demandesAnnulation->count()); ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date Exécution</th>
                                <th>Professeur</th>
                                <th>Module & Groupe</th>
                                <th>Motif</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $demandesAnnulation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($demande->jour); ?> Ã  <?php echo e(substr($demande->heure_debut, 0, 5)); ?></td>
                                    <td><?php echo e($demande->professeur->nom_complet); ?></td>
                                    <td>
                                        <strong><?php echo e($demande->module->nom); ?></strong><br>
                                        <small class="text-muted"><?php echo e($demande->groupe->nom); ?></small>
                                    </td>
                                    <td><span class="text-danger"><?php echo e($demande->motif_annulation); ?></span></td>
                                    <td class="text-end">
                                        <form action="<?php echo e(route('admin.emplois.approve', $demande)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-success" title="Approuver">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form action="<?php echo e(route('admin.emplois.reject', $demande)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-danger" title="Refuser">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">Aucune demande en attente.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières Validations -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Dernières Séances Validées</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Professeur</th>
                                <th>Module</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentValidations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $validation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php if($validation->emploiDuTemps): ?>
                                <tr>
                                    <td><?php echo e($validation->date->format('d/m/Y')); ?></td>
                                    <td><?php echo e($validation->emploiDuTemps->professeur->nom_complet); ?></td>
                                    <td><?php echo e($validation->emploiDuTemps->module->nom); ?></td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="3" class="text-center text-muted py-3">Aucune validation récente.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières Absences -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-person-x me-2"></i>Dernières Absences Stagiaires</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Stagiaire</th>
                                <th>Module</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentAbsences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $absence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php if($absence->seanceRealisation && $absence->seanceRealisation->emploiDuTemps): ?>
                                <tr>
                                    <td><?php echo e($absence->seanceRealisation->date->format('d/m/Y')); ?></td>
                                    <td><?php echo e($absence->etudiant->nom_complet); ?></td>
                                    <td><small><?php echo e($absence->seanceRealisation->emploiDuTemps->module->nom); ?></small></td>
                                    <td>
                                        <span class="badge <?php echo e($absence->status == 'Justifié' ? 'bg-warning text-dark' : 'bg-danger'); ?>">
                                            <?php echo e($absence->status); ?>

                                        </span>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Aucune absence récente répertoriée.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\aminelabzae\OneDrive\Documents\Programmation\2em-annee\laravel\emploi-du-temps (8)\emploi-du-temps\resources\views/admin/professeurs/paie.blade.php ENDPATH**/ ?>