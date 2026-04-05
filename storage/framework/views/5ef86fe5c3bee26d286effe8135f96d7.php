?

<?php $__env->startSection('title', 'Grille Emploi du Temps'); ?>
<?php $__env->startSection('subtitle', $groupe ? $groupe->nom . ' - ' . $groupe->filiere->nom : 'Sélectionnez un groupe'); ?>

<?php $__env->startSection('actions'); ?>
    <?php if(request('view') === 'trashed'): ?>
        <a href="<?php echo e(route('admin.emplois.grille', ['groupe_id' => request('groupe_id')])); ?>" class="btn btn-secondary me-2">
            <i class="bi bi-arrow-left me-2"></i> Grille Actuelle
        </a>
    <?php else: ?>
        <a href="<?php echo e(route('admin.emplois.grille', ['groupe_id' => request('groupe_id'), 'view' => 'trashed'])); ?>" class="btn btn-warning me-2">
            <i class="bi bi-archive me-2"></i> Grilles Archivées
        </a>
    <?php endif; ?>
    <?php if($groupe): ?>
        <a href="<?php echo e(route('admin.emplois.pdf', ['groupe_id' => $groupe->id, 'view' => request('view')])); ?>" class="btn btn-danger me-2">
            <i class="bi bi-file-pdf me-2"></i> Export PDF
        </a>
    <?php endif; ?>
    <a href="<?php echo e(route('admin.emplois.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Nouvelle séance
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Sélection du groupe -->
<div class="card mb-4">
    <div class="card-body">
        <form action="<?php echo e(route('admin.emplois.grille')); ?>" method="GET" class="row g-3 align-items-end">
            <?php if(request('view') === 'trashed'): ?>
                <input type="hidden" name="view" value="trashed">
            <?php endif; ?>
            <div class="col-md-8">
                <label for="groupe_id" class="form-label">Sélectionner un groupe</label>
                <select class="form-select" id="groupe_id" name="groupe_id" onchange="this.form.submit()">
                    <option value="">-- Choisir un groupe --</option>
                    <?php $__currentLoopData = $groupes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g->id); ?>" <?php echo e($groupeId == $g->id ? 'selected' : ''); ?>>
                            <?php echo e($g->nom); ?> - <?php echo e($g->filiere->nom); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-2"></i> Afficher
                </button>
            </div>
        </form>
    </div>
</div>

<?php if($groupe): ?>
<!-- Grille emploi du temps -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead style="background-color: #003366; color: white;">
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
                                <strong><?php echo e($heureDebut); ?></strong><br><small class="text-muted"><?php echo e($heureFin); ?></small>
                            </td>
                            <?php $__currentLoopData = $jours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $seance = isset($emplois[$jour])
                                        ? $emplois[$jour]->first(function($s) use ($heureDebut) {
                                            return substr($s->heure_debut, 0, 5) == $heureDebut;
                                        })
                                        : null;
                                ?>
                                <td class="p-2 <?php echo e($seance ? ($seance->is_examen ? 'bg-danger bg-opacity-10' : 'bg-primary bg-opacity-10') : ''); ?>">
                                    <?php if($seance): ?>
                                        <div class="p-2 rounded bg-white shadow-sm border-start border-4 <?php echo e($seance->is_examen ? 'border-danger' : 'border-primary'); ?>">
                                            <?php if($seance->is_examen): ?>
                                                <span class="badge bg-danger mb-1">EXAMEN</span>
                                            <?php endif; ?>
                                            <strong class="d-block text-dark"><?php echo e($seance->module->nom); ?></strong>
                                            <small class="d-block text-secondary">
                                                <i class="bi bi-person me-1"></i><?php echo e($seance->professeur->nom_complet); ?>

                                            </small>
                                            <small class="d-block text-secondary">
                                                <?php if($seance->type_seance === 'Teams'): ?>
                                                    <span class="badge bg-info p-1"><i class="bi bi-laptop me-1"></i>Teams</span>
                                                <?php else: ?>
                                                    <i class="bi bi-building me-1"></i><?php echo e($seance->salle->nom); ?>

                                                <?php endif; ?>
                                            </small>
                                            <div class="mt-2 text-center">
                                                <?php if(request('view') === 'trashed'): ?>
                                                    <span class="badge bg-danger">Archivée le <?php echo e($seance->deleted_at->format('d/m/Y')); ?></span>
                                                <?php else: ?>
                                                    <a href="<?php echo e(route('admin.emplois.edit', $seance)); ?>" class="btn btn-sm btn-outline-warning py-0 px-2" title="Modifier">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
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
<div class="alert alert-info border-0 shadow-sm">
    <i class="bi bi-info-circle me-2"></i> Veuillez sélectionner un groupe pour afficher son emploi du temps.
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/emplois/grille.blade.php ENDPATH**/ ?>