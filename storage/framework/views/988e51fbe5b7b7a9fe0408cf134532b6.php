<?php $__env->startSection('title', 'Professeurs'); ?>
<?php $__env->startSection('subtitle', 'Gestion des professeurs'); ?>

<?php $__env->startSection('actions'); ?>
    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-file-earmark-excel me-2"></i> Importer Excel
    </button>
    <a href="<?php echo e(route('admin.professeurs.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Nouveau professeur
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Liste des professeurs</h5>
        <form method="GET" class="mt-3 d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher par nom, prénom, email, téléphone ou spécialité..." value="<?php echo e(request('search')); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Rechercher
            </button>
            <?php if(request('search')): ?>
                <a href="<?php echo e(route('admin.professeurs.index')); ?>" class="btn btn-secondary ms-2">
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
                        <th>ID</th>
                        <th>Nom complet</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Spécialité</th>
                        <th>Masse Horaire</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $professeurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $professeur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e(($professeurs->currentPage() - 1) * $professeurs->perPage() + $loop->iteration); ?></td>
                            <td>
                                <strong><?php echo e($professeur->prenom); ?> <?php echo e($professeur->nom); ?></strong>
                            </td>
                            <td><?php echo e($professeur->email); ?></td>
                            <td><?php echo e($professeur->telephone ?? '-'); ?></td>
                            <td><?php echo e($professeur->specialite ?? '-'); ?></td>
                            <td>
                                <?php
                                    $heuresHebdo = $professeur->getHeuresHebdomadairesActuelles();
                                    $heuresMensuelles = $heuresHebdo; // Synchronisation
                                    $maxHeuresMensuel = $professeur->max_heures_mensuel;
                                ?>
                                <div class="mb-1">
                                    <span class="badge bg-primary">
                                        <?php echo e(\App\Models\EmploiDuTemps::formatHeures($heuresHebdo)); ?> / mois
                                    </span>
                                </div>
                                <div class="small">
                                    <?php if($maxHeuresMensuel): ?>
                                        <span class="text-info">Limite: <?php echo e($maxHeuresMensuel); ?>h/mois</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if($professeur->actif): ?>
                                    <span class="badge bg-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo e(route('admin.professeurs.show', $professeur)); ?>" class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.professeurs.edit', $professeur)); ?>" class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?php echo e(route('admin.professeurs.destroy', $professeur)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce professeur ?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-info-circle me-2"></i>Aucun professeur trouvé
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
    <?php if($professeurs->hasPages()): ?>
        <div class="card-footer px-4 py-3">
            <?php echo e($professeurs->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container { z-index: 1061 !important; } /* Fix Select2 inside Bootstrap modal */
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        background-color: var(--ofppt-blue);
        color: white; border: none;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        if ($('#import_module_ids').length) {
            $('#import_module_ids').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#importModal'),
                width: '100%',
                placeholder: 'Sélectionner des modules...',
                closeOnSelect: false
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>


<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.professeurs.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bi bi-file-earmark-excel me-2"></i>Importer des professeurs
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Fichier Excel (.xlsx, .xls) ou CSV</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text mt-2 text-info">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Un compte utilisateur sera automatiquement créé pour chaque professeur.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="module_ids" class="form-label">Modules à assigner (Optionnel)</label>
                        <select name="module_ids[]" id="import_module_ids" class="form-select" multiple>
                            <?php
                                $groupedModules = collect();
                                foreach($modules as $module) {
                                    $modFilieres = $module->filieres ?? collect();
                                    if($modFilieres->isEmpty()) {
                                        $groupedModules->push(['filiere' => 'Modules Généraux / Sans Filière', 'module' => $module]);
                                    } else {
                                        foreach($modFilieres as $filiere) {
                                            $groupedModules->push(['filiere' => $filiere->nom . ' (' . $filiere->code . ')', 'module' => $module]);
                                        }
                                    }
                                }
                                $allGroups = $groupedModules->groupBy('filiere');
                            ?>

                            <?php $__currentLoopData = $allGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filiereName => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <optgroup label="<?php echo e($filiereName); ?>">
                                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item['module']->id); ?>">
                                            [<?php echo e($item['module']->code); ?>] <?php echo e($item['module']->nom); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </optgroup>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div class="form-text mt-1">
                            <i class="bi bi-info-circle me-1"></i>
                            Ces modules seront assignés à <strong>tous</strong> les professeurs du fichier.
                        </div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Colonnes attendues :</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>Mle / Matricule</strong> (Requis)</li>
                            <li>(Supporte <em>Mle Affecté Présentiel/Syn Actif</em>)</li>
                            <li><strong>Nom</strong> & <strong>Prénom</strong> (ou <em>Formateur</em>)</li>
                            <li>(Supporte <em>Formateur Affecté Présentiel/Syn Actif</em>)</li>
                            <li><strong>Groupe</strong> & <strong>Module</strong> (Optionnel - Affectation)</li>
                            <li>Email (Auto-généré si vide)</li>
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



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/professeurs/index.blade.php ENDPATH**/ ?>