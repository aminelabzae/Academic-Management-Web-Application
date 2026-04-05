

<?php $__env->startSection('title', 'Modifier Stagiaire'); ?>
<?php $__env->startSection('subtitle', $etudiant->nom_complet); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Modifier le stagiaire</h5></div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.etudiants.update', $etudiant)); ?>" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="cef" class="form-label">CEF <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['cef'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="cef" name="cef" value="<?php echo e(old('cef', $etudiant->cef)); ?>" required>
                            <?php $__errorArgs = ['cef'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-4">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo e(old('nom', $etudiant->nom)); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo e(old('prenom', $etudiant->prenom)); ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo e(old('email', $etudiant->email)); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo e(old('telephone', $etudiant->telephone)); ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date_naissance" class="form-label">Date de naissance</label>
                            <input type="date" class="form-control" id="date_naissance" name="date_naissance"
                                   value="<?php echo e(old('date_naissance', $etudiant->date_naissance?->format('Y-m-d'))); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="groupe_id" class="form-label">Groupe <span class="text-danger">*</span></label>
                            <select class="form-select" id="groupe_id" name="groupe_id" required>
                                <?php $__currentLoopData = $groupes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($groupe->id); ?>" <?php echo e(old('groupe_id', $etudiant->groupe_id) == $groupe->id ? 'selected' : ''); ?>>
                                        <?php echo e($groupe->nom); ?> - <?php echo e($groupe->filiere->nom); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1"
                                   <?php echo e(old('actif', $etudiant->actif) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="actif">Stagiaire actif</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo e(route('admin.etudiants.index')); ?>" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Annuler</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i> Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/etudiants/edit.blade.php ENDPATH**/ ?>