<?php $__env->startSection('title', 'Gestion des Modules'); ?>
<?php $__env->startSection('subtitle', 'Liste de tous les modules'); ?>

<?php $__env->startSection('actions'); ?>
    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-file-earmark-excel me-2"></i> Importer Excel
    </button>
    <a href="<?php echo e(route('admin.modules.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Nouveau module
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Liste des modules</h5>
        <form method="GET" class="mt-3 d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher par code, nom ou filière..." value="<?php echo e(request('search')); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Rechercher
            </button>
            <?php if(request('search')): ?>
                <a href="<?php echo e(route('admin.modules.index')); ?>" class="btn btn-secondary ms-2">
                    <i class="bi bi-x-circle"></i> Effacer
                </a>
            <?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Nom</th>
                        <th>Filières</th>
                        <th>Semestre</th>
                        <th>Heures Module</th>
                        <th>Coefficient</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($module->code); ?></strong></td>
                            <td><?php echo e($module->nom); ?></td>
                            <td>
                                <?php $__currentLoopData = $module->filieres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filiere): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge bg-info text-dark" title="<?php echo e($filiere->nom); ?>"><?php echo e($filiere->code); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                            <td><span class="badge bg-secondary">S<?php echo e($module->semestre); ?></span></td>
                            <td>
                                <?php if($module->masse_horaire): ?>
                                    <?php
                                        $heuresHebdo = $module->getHeuresHebdomadairesActuelles();
                                        $heuresTotales = $module->getHeuresTotalesByGroupe(); // Toutes séances
                                        $ratio = $module->masse_horaire > 0 ? ($heuresTotales / $module->masse_horaire) : 0;
                                    ?>
                                    <div class="mb-1">
                                        <span class="badge bg-primary">
                                            <?php echo e(\App\Models\EmploiDuTemps::formatHeures($heuresHebdo)); ?> / mois
                                        </span>
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        Total consommé: <?php echo e(\App\Models\EmploiDuTemps::formatHeures($heuresTotales)); ?> / <?php echo e($module->masse_horaire); ?>h
                                    </small>
                                <?php else: ?>
                                    <span class="text-muted">∞</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($module->coefficient); ?></td>
                            <td>
                                <a href="<?php echo e(route('admin.modules.syllabus.index', $module)); ?>" class="btn btn-sm btn-outline-primary" title="Syllabus/Chapitres"><i class="bi bi-list-check"></i></a>
                                <a href="<?php echo e(route('admin.modules.show', $module)); ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                <a href="<?php echo e(route('admin.modules.edit', $module)); ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                <form action="<?php echo e(route('admin.modules.destroy', $module)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce module ?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Aucun module trouvé</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($modules->hasPages()): ?><div class="card-footer"><?php echo e($modules->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>


<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.modules.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bi bi-file-earmark-excel me-2"></i>Importer des modules
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Fichier Excel (.xlsx, .xls) ou CSV</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text mt-2 text-info">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Séparez les codes filières par des virgules pour lier un module à plusieurs filières.
                        </div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Colonnes attendues :</strong>
                        <ul class="mb-0 mt-2">
                            <li>Code (Unique)</li>
                            <li>Nom</li>
                            <li>Semestre</li>
                            <li>Masse Horaire</li>
                            <li>Coefficient</li>
                            <li>Codes Filières (ex: "TDI, TRI")</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload me-2"></i>Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/modules/index.blade.php ENDPATH**/ ?>