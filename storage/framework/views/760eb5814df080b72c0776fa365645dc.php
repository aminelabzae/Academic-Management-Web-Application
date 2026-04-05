

<?php $__env->startSection('title', 'Gestion des Annonces'); ?>
<?php $__env->startSection('subtitle', 'Annonces envoyées aux stagiaires'); ?>

<?php $__env->startSection('actions'); ?>
    <a href="<?php echo e(route('admin.annonces.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Nouvelle annonce
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Contenu</th>
                        <th>Créée par</th>
                        <th>Date</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $annonces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $annonce): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($annonce->titre); ?></strong></td>
                            <td><?php echo e(Str::limit($annonce->contenu, 80)); ?></td>
                            <td><?php echo e($annonce->user->name); ?></td>
                            <td><?php echo e($annonce->created_at->format('d/m/Y H:i')); ?></td>
                            <td>
                                <form action="<?php echo e(route('admin.annonces.destroy', $annonce)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette annonce ?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Aucune annonce trouvée</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($annonces->hasPages()): ?>
        <div class="card-footer px-4 py-3">
            <?php echo e($annonces->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/annonces/index.blade.php ENDPATH**/ ?>