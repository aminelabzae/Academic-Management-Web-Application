<?php $__env->startSection('title', 'Gestion des Filières'); ?>
<?php $__env->startSection('subtitle', 'Liste de toutes les filières'); ?>

<?php $__env->startSection('actions'); ?>
    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-file-earmark-excel me-2"></i> Importer Excel
    </button>
    <a href="<?php echo e(route('admin.filieres.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Nouvelle filière
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i><?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher par code, nom ou secteur..." value="<?php echo e(request('search')); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Rechercher
            </button>
            <?php if(request('search')): ?>
                <a href="<?php echo e(route('admin.filieres.index')); ?>" class="btn btn-secondary ms-2">
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
                        <th>Niveau</th>
                        <th>Durée</th>
                        <th>Groupes</th>
                        <th>Modules</th>
                        <th>Statut</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $filieres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filiere): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($filiere->code); ?></strong></td>
                            <td><?php echo e($filiere->nom); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo e($filiere->niveau); ?></span>
                            </td>
                            <td><?php echo e($filiere->duree_formation); ?> ans</td>
                            <td>
                                <span class="badge bg-secondary"><?php echo e($filiere->groupes_count); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?php echo e($filiere->modules_count); ?></span>
                            </td>
                            <td>
                                <?php if($filiere->active): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.filieres.show', $filiere)); ?>" class="btn btn-sm btn-outline-info" title="Voir">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.filieres.edit', $filiere)); ?>" class="btn btn-sm btn-outline-warning" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('admin.filieres.destroy', $filiere->id)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Aucune filière trouvée
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($filieres->hasPages()): ?>
        <div class="card-footer px-4 py-3">
            <?php echo e($filieres->links()); ?>

        </div>
    <?php endif; ?>
</div>


<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.filieres.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bi bi-file-earmark-excel me-2"></i>Importer des filières
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Fichier Excel (.xlsx, .xls) ou CSV</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text mt-2 text-info">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Vous pouvez désormais importer directement vos fichiers <strong>.xlsx</strong>.
                        </div>
                        <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Colonnes attendues dans le fichier :</strong>
                        <ul class="mb-0 mt-2">
                            <li>Année</li>
                            <li>Niveau</li>
                            <li>Secteur</li>
                            <li>Code Filière</li>
                            <li>Filière</li>
                            <li>Type de formation</li>
                        </ul>
                        <hr class="my-2">
                        <small class="text-muted">Les filières avec un code déjà existant seront ignorées (pas de doublons).</small>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/filieres/index.blade.php ENDPATH**/ ?>