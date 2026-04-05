

<?php $__env->startSection('title', 'Gestion des Stagiaires'); ?>
<?php $__env->startSection('subtitle', 'Liste de tous les stagiaires'); ?>

<?php $__env->startSection('actions'); ?>
    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-file-earmark-excel me-2"></i> Importer
    </button>
    <a href="<?php echo e(route('admin.etudiants.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Nouveau stagiaire
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher par CEF, nom, prénom, email ou groupe..." value="<?php echo e(request('search')); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Rechercher
            </button>
            <?php if(request('search')): ?>
                <a href="<?php echo e(route('admin.etudiants.index')); ?>" class="btn btn-secondary ms-2">
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
                        <th>CEF</th>
                        <th>Nom complet</th>
                        <th>Groupe</th>
                        <th>Filière</th>
                        <th>Email</th>
                        <th>Statut</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $etudiants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $etudiant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($etudiant->cef); ?></strong></td>
                            <td><?php echo e($etudiant->prenom); ?> <?php echo e($etudiant->nom); ?></td>
                            <td><span class="badge bg-info"><?php echo e($etudiant->groupe->nom); ?></span></td>
                            <td><?php echo e($etudiant->groupe->filiere->nom); ?></td>
                            <td><?php echo e($etudiant->email ?? '-'); ?></td>
                            <td>
                                <?php if($etudiant->actif): ?><span class="badge bg-success">Actif</span>
                                <?php else: ?><span class="badge bg-danger">Inactif</span><?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.etudiants.show', $etudiant)); ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                <a href="<?php echo e(route('admin.etudiants.edit', $etudiant)); ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                <form action="<?php echo e(route('admin.etudiants.destroy', $etudiant)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce stagiaire ?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Aucun stagiaire trouvé</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($etudiants->hasPages()): ?>
        <div class="card-footer px-4 py-3">
            <?php echo e($etudiants->links()); ?>

        </div>
    <?php endif; ?>
</div>


<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.etudiants.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bi bi-file-earmark-excel me-2"></i>Importer des stagiaires
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Fichier Excel (.xlsx, .xls)</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls" required>
                        <div class="form-text mt-2 text-info">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Un compte utilisateur sera automatiquement créé pour chaque stagiaire avec l'email <strong>CEF@ofppt-emploi.ma</strong>.
                        </div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Colonnes attendues (Première ligne) :</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>CEF</strong> (Requis)</li>
                            <li><strong>Nom</strong> (Requis)</li>
                            <li><strong>Prenom</strong> (Requis)</li>
                            <li><strong>Groupe</strong> (Requis - Nom du groupe)</li>
                            <li>DateNaissance (Optionnel)</li>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\aminelabzae\OneDrive\Documents\Programmation\2em-annee\laravel\emploi-du-temps (8)\emploi-du-temps\resources\views/admin/etudiants/index.blade.php ENDPATH**/ ?>