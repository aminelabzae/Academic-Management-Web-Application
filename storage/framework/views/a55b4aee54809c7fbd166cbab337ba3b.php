

<?php $__env->startSection('title', 'Gestion des Utilisateurs'); ?>
<?php $__env->startSection('subtitle', 'Liste de tous les utilisateurs'); ?>

<?php $__env->startSection('actions'); ?>
    <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Nouvel utilisateur
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher par nom, email ou rôle..." value="<?php echo e(request('search')); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Rechercher
            </button>
            <?php if(request('search')): ?>
                <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary ms-2">
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
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date de création</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($user->name); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($user->role === 'head_admin' ? 'dark' : ($user->role === 'admin' ? 'danger' : ($user->role === 'professeur' ? 'warning' : 'info'))); ?>">
                                    <?php echo e($user->role === 'head_admin' ? 'Head Admin' : ucfirst($user->role)); ?>

                                </span>
                            </td>
                            <td><?php echo e($user->created_at->format('d/m/Y')); ?></td>
                            <td>
                                <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                                <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Modifier Mot de Passe
                                </a>
                                <?php if(Auth::id() !== $user->id && (Auth::user()->role === 'head_admin' || $user->role !== 'head_admin')): ?>
                                    <form action="<?php echo e(route('admin.users.destroy', $user)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Aucun utilisateur trouvé</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($users->hasPages()): ?>
        <div class="card-footer d-flex justify-content-center">
            <?php echo e($users->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\aminelabzae\OneDrive\Documents\Programmation\2em-annee\laravel\emploi-du-temps (8)\emploi-du-temps\resources\views/admin/users/index.blade.php ENDPATH**/ ?>