?

<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Modifier le Mot de Passe de <?php echo e($user->name); ?></h1>
    <form action="<?php echo e(route('admin.users.update', $user)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <?php if(Auth::id() === $user->id): ?>
            <div class="form-group">
                <label for="current_password">Mot de Passe Actuel</label>
                <input type="password" name="current_password" id="current_password" class="form-control" required>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="password">Nouveau Mot de Passe</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirmer le Nouveau Mot de Passe</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>

        <?php if(auth()->user()->role === 'head_admin' && Auth::id() !== $user->id): ?>
            <div class="form-group mt-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1"
                           <?php echo e(old('actif', $user->actif) ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="actif">Compte actif</label>
                </div>
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-success">Mettre à Jour</button>
        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/users/edit.blade.php ENDPATH**/ ?>