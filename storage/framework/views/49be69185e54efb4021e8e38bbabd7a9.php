?

<?php $__env->startSection('title', 'Modifier Module'); ?>
<?php $__env->startSection('subtitle', $module->nom); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Modifier le module</h5></div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.modules.update', $module)); ?>" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="code" name="code" value="<?php echo e(old('code', $module->code)); ?>" required>
                            <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-8">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo e(old('nom', $module->nom)); ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="filiere_ids" class="form-label">Filières <span class="text-danger">*</span></label>
                            <select class="form-select <?php $__errorArgs = ['filiere_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="filiere_ids" name="filiere_ids[]" multiple required style="height: 120px;">
                                <?php $selectedFilieres = old('filiere_ids', $module->filieres->pluck('id')->toArray()); ?>
                                <?php $__currentLoopData = $filieres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filiere): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($filiere->id); ?>" <?php echo e(in_array($filiere->id, $selectedFilieres) ? 'selected' : ''); ?>>
                                        <?php echo e($filiere->code); ?> - <?php echo e($filiere->nom); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-muted">Maintenez Ctrl pour sélectionner plusieurs filières</small>
                            <?php $__errorArgs = ['filiere_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label for="semestre" class="form-label">Semestre <span class="text-danger">*</span></label>
                            <select class="form-select" id="semestre" name="semestre" required>
                                <?php for($i = 1; $i <= 4; $i++): ?>
                                    <option value="<?php echo e($i); ?>" <?php echo e(old('semestre', $module->semestre) == $i ? 'selected' : ''); ?>>Semestre <?php echo e($i); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="coefficient" class="form-label">Coefficient</label>
                            <input type="number" class="form-control" id="coefficient" name="coefficient"
                                   value="<?php echo e(old('coefficient', $module->coefficient)); ?>" min="0" max="10" step="0.5">
                        </div>
                        <div class="col-md-6">
                            <label for="masse_horaire" class="form-label">Masse Horaire (Total)</label>
                            <input type="number" class="form-control <?php $__errorArgs = ['masse_horaire'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="masse_horaire" name="masse_horaire"
                                   value="<?php echo e(old('masse_horaire', $module->masse_horaire)); ?>" min="0">
                            <?php $__errorArgs = ['masse_horaire'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo e(route('admin.modules.index')); ?>" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Annuler</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i> Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/modules/edit.blade.php ENDPATH**/ ?>