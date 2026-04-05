

<?php $__env->startSection('title', $etudiant->nom_complet); ?>
<?php $__env->startSection('subtitle', 'Détails du stagiaire'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-person me-2"></i>Informations</h5></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="150">CEF:</th><td><?php echo e($etudiant->cef); ?></td></tr>
                    <tr><th>Groupe:</th><td><span class="badge bg-info"><?php echo e($etudiant->groupe->nom); ?></span></td></tr>
                    <tr><th>Filière:</th><td><?php echo e($etudiant->groupe->filiere->nom); ?></td></tr>
                    <tr><th>Email:</th><td><?php echo e($etudiant->email ?? '-'); ?></td></tr>
                    <tr><th>Téléphone:</th><td><?php echo e($etudiant->telephone ?? '-'); ?></td></tr>
                    <tr><th>Date naissance:</th><td><?php echo e($etudiant->date_naissance?->format('d/m/Y') ?? '-'); ?></td></tr>
                    <tr><th>Statut:</th><td>
                        <?php if($etudiant->actif): ?><span class="badge bg-success">Actif</span>
                        <?php else: ?><span class="badge bg-danger">Inactif</span><?php endif; ?>
                    </td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="<?php echo e(route('admin.etudiants.index')); ?>" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Retour</a>
    <a href="<?php echo e(route('admin.etudiants.edit', $etudiant)); ?>" class="btn btn-warning"><i class="bi bi-pencil me-2"></i> Modifier</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/etudiants/show.blade.php ENDPATH**/ ?>