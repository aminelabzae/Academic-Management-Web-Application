

<?php $__env->startSection('title', 'Gestion des Absences'); ?>
<?php $__env->startSection('subtitle', 'Suivi et justification des absences stagiaires'); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('admin.absences.index')); ?>" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Groupe</label>
                <select name="groupe_id" class="form-select">
                    <option value="">Tous les groupes</option>
                    <?php $__currentLoopData = $groupes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($groupe->id); ?>" <?php echo e(request('groupe_id') == $groupe->id ? 'selected' : ''); ?>>
                            <?php echo e($groupe->nom); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Stagiaire</label>
                <select name="etudiant_id" class="form-select">
                    <option value="">Tous les stagiaires</option>
                    <?php $__currentLoopData = $etudiants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $etudiant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($etudiant->id); ?>" <?php echo e(request('etudiant_id') == $etudiant->id ? 'selected' : ''); ?>>
                            <?php echo e($etudiant->nom_complet); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Statut</label>
                <select name="status" class="form-select">
                    <option value="">Tous</option>
                    <option value="Absent" <?php echo e(request('status') == 'Absent' ? 'selected' : ''); ?>>Absent</option>
                    <option value="Justifié" <?php echo e(request('status') == 'Justifié' ? 'selected' : ''); ?>>Justifié</option>
                    <option value="Présent" <?php echo e(request('status') == 'Présent' ? 'selected' : ''); ?>>Présent</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Depuis le</label>
                <input type="date" name="date_debut" class="form-control" value="<?php echo e(request('date_debut')); ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-2"></i>Filtrer
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Stagiaire</th>
                        <th>Groupe</th>
                        <th>Module / Professeur</th>
                        <th>Statut</th>
                        <th>Commentaire</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <?php echo e(\Carbon\Carbon::parse($attendance->seanceRealisation->date)->format('d/m/Y')); ?>

                                <br><small class="text-muted"><?php echo e($attendance->seanceRealisation->emploiDuTemps->heure_debut); ?></small>
                            </td>
                            <td><strong><?php echo e($attendance->etudiant->nom_complet); ?></strong></td>
                            <td><?php echo e($attendance->etudiant->groupe->nom); ?></td>
                            <td>
                                <?php echo e($attendance->seanceRealisation->emploiDuTemps->module->nom); ?>

                                <br><small class="text-muted">Par <?php echo e($attendance->seanceRealisation->emploiDuTemps->professeur->nom_complet); ?></small>
                            </td>
                            <td>
                                <?php if($attendance->status == 'Présent'): ?>
                                    <span class="badge bg-success">Présent</span>
                                <?php elseif($attendance->status == 'Absent'): ?>
                                    <span class="badge bg-danger">Absent</span>
                                <?php else: ?>
                                    <span class="badge bg-info">Justifié</span>
                                <?php endif; ?>
                            </td>
                            <td><small><?php echo e($attendance->commentaire ?: '-'); ?></small></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <?php if($attendance->status == 'Absent'): ?>
                                        <form action="<?php echo e(route('admin.absences.justify', $attendance)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-success" title="Justifier l'absence">
                                                <i class="bi bi-shield-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo e($attendance->id); ?>" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>
                                
                                <!-- Modal Edit -->
                                <div class="modal fade" id="modalEdit<?php echo e($attendance->id); ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="<?php echo e(route('admin.absences.update', $attendance)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Modifier le statut de présence</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Statut</label>
                                                        <select name="status" class="form-select">
                                                            <option value="Présent" <?php echo e($attendance->status == 'Présent' ? 'selected' : ''); ?>>Présent</option>
                                                            <option value="Absent" <?php echo e($attendance->status == 'Absent' ? 'selected' : ''); ?>>Absent</option>
                                                            <option value="Justifié" <?php echo e($attendance->status == 'Justifié' ? 'selected' : ''); ?>>Justifié</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Commentaire / Justification</label>
                                                        <textarea name="commentaire" class="form-control" rows="3"><?php echo e($attendance->commentaire); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Aucun enregistrement trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($attendances->hasPages()): ?>
        <div class="card-footer px-4 py-3">
            <?php echo e($attendances->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cibler tous les champs de commentaire dans les modals
    const textareas = document.querySelectorAll('textarea[name="commentaire"]');
    
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            // Trouver le menu déroulant "status" correspondant dans le même formulaire
            const form = this.closest('form');
            if (form) {
                const statusSelect = form.querySelector('select[name="status"]');
                // Si l'utilisateur commence à taper et que c'est sur "Absent", on passe à "Justifié"
                if (statusSelect && this.value.trim().length > 0 && statusSelect.value === 'Absent') {
                    statusSelect.value = 'Justifié';
                }
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/absences/index.blade.php ENDPATH**/ ?>