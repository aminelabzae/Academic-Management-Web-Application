<?php $__env->startSection('title', 'Groupes'); ?>
<?php $__env->startSection('subtitle', 'Gestion des groupes de stagiaires'); ?>

<?php $__env->startSection('actions'); ?>
    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-file-earmark-excel me-2"></i> Importer Excel
    </button>
    <a href="<?php echo e(route('admin.groupes.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Nouveau groupe
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-people me-2"></i>Liste des groupes</h5>
        <form method="GET" class="mt-3 d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher par nom ou filière..." value="<?php echo e(request('search')); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Rechercher
            </button>
            <?php if(request('search')): ?>
                <a href="<?php echo e(route('admin.groupes.index')); ?>" class="btn btn-secondary ms-2">
                    <i class="bi bi-x-circle"></i> Effacer
                </a>
            <?php endif; ?>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Filière</th>
                        <th>Stagiaires</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $groupes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <strong><?php echo e($groupe->nom); ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo e($groupe->filiere->nom ?? 'N/A'); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?php echo e($groupe->etudiants_count); ?> stagiaire(s)</span>
                            </td>
                            <td>
                                <?php if($groupe->actif): ?>
                                    <span class="badge bg-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo e(route('admin.groupes.show', $groupe)); ?>" class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.groupes.edit', $groupe)); ?>" class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?php echo e(route('admin.groupes.destroy', $groupe)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce groupe ?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-info-circle me-2"></i>Aucun groupe trouvé
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
    <?php if($groupes->hasPages()): ?>
        <div class="card-footer px-4 py-3">
            <?php echo e($groupes->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>


<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.groupes.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bi bi-file-earmark-excel me-2"></i>Importer des groupes
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Fichier Excel (.xlsx, .xls) ou CSV</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text mt-2 text-info">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Utilisez le <strong>Code Filière</strong> pour lier automatiquement les groupes.
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
                        <strong>Colonnes attendues :</strong>
                        <ul class="mb-0 mt-2">
                            <li>Nom</li>
                            <li>Code Filière</li>
                            <li>Année</li>
                            <li>Effectif</li>
                            <li>Année Scolaire</li>
                        </ul>
                        <hr class="my-2">
                        <small class="text-muted">Si un groupe existe déjà (même Nom + Année Scolaire), il sera mis à jour.</small>
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



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\aminelabzae\OneDrive\Documents\Programmation\2em-annee\laravel\emploi-du-temps (8)\emploi-du-temps\resources\views/admin/groupes/index.blade.php ENDPATH**/ ?>