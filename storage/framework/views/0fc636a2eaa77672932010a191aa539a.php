

<?php $__env->startSection('title', 'Nouvelle Annonce'); ?>
<?php $__env->startSection('subtitle', 'Créer une annonce pour les stagiaires'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-megaphone me-2"></i>Rédiger l'annonce</h5></div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.annonces.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php $__errorArgs = ['titre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="titre" name="titre" value="<?php echo e(old('titre')); ?>" required placeholder="Ex: Changement d'horaires, Jour férié...">
                        <?php $__errorArgs = ['titre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-4">
                        <label for="contenu" class="form-label">Contenu <span class="text-danger">*</span></label>
                        <textarea class="form-control <?php $__errorArgs = ['contenu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="contenu" name="contenu" rows="6" required placeholder="Écrivez le contenu de l'annonce..."><?php echo e(old('contenu')); ?></textarea>
                        <?php $__errorArgs = ['contenu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo e(route('admin.annonces.index')); ?>" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Annuler</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i> Publier l'annonce</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/annonces/create.blade.php ENDPATH**/ ?>