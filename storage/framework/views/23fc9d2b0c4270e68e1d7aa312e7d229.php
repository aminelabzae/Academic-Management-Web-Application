<?php $__env->startSection('title', 'Avancement des Modules'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-primary text-white">
            <div class="card-body py-4">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                        <i class="bi bi-graph-up-arrow fs-3"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">Avancement des Modules</h4>
                        <p class="mb-0 opacity-75">Suivez la progression pédagogique de vos cours</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php $__empty_1 = true; $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-light text-primary border border-primary border-opacity-25 mb-2"><?php echo e($module->code); ?></span>
                            <h5 class="fw-bold text-dark mb-0"><?php echo e($module->nom); ?></h5>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold text-primary mb-0"><?php echo e($module->progress_syllabus); ?>%</h3>
                        </div>
                    </div>
                    <div class="progress rounded-pill mb-3" style="height: 10px; background-color: #e9ecef;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                             role="progressbar" 
                             style="width: <?php echo e($module->progress_syllabus); ?>%" 
                             aria-valuenow="<?php echo e($module->progress_syllabus); ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>
                
                <div class="card-body px-0 py-0 overflow-auto" style="max-height: 400px;">
                    <div class="list-group list-group-flush border-top border-bottom">
                        <?php $__empty_2 = true; $__currentLoopData = $module->syllabusItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                            <?php $isDone = $item->realisations->isNotEmpty(); ?>
                            <div class="list-group-item border-0 py-3 px-4 <?php echo e($isDone ? 'bg-success bg-opacity-10' : ''); ?>">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center overflow-hidden">
                                        <?php if($isDone): ?>
                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 28px; height: 28px;">
                                                <i class="bi bi-check-lg fw-bold" style="font-size: 0.9rem;"></i>
                                            </div>
                                        <?php else: ?>
                                            <div class="bg-light text-muted border rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 28px; height: 28px;">
                                                <span class="small fw-bold"><?php echo e($loop->iteration); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="text-truncate">
                                            <span class="d-block text-truncate <?php echo e($isDone ? 'text-success fw-medium text-decoration-line-through' : 'text-dark'); ?>">
                                                <?php echo e($item->titre); ?>

                                            </span>
                                        </div>
                                    </div>
                                    <div class="ms-2 flex-shrink-0">
                                        <span class="badge <?php echo e($isDone ? 'bg-success' : 'bg-light text-muted border'); ?> rounded-pill" style="font-size: 0.75rem;">
                                            <?php echo e($item->poids_pourcentage); ?>%
                                        </span>
                                    </div>
                                </div>
                                <?php if($isDone && $item->realisations->first()): ?>
                                    <div class="ms-5 mt-1 small text-success opacity-75">
                                        <i class="bi bi-calendar-check me-1"></i> Terminé le <?php echo e($item->realisations->first()->date->translatedFormat('d M Y')); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-journal-x fs-1 text-muted opacity-25 d-block mb-3"></i>
                                <p class="text-muted mb-0">Aucun chapitre défini pour ce module.</p>
                                <a href="<?php echo e(route('professeur.emploi')); ?>" class="btn btn-sm btn-outline-primary mt-3">
                                    <i class="bi bi-plus"></i> Ajouter un chapitre
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-footer bg-light border-0 py-3 px-4">
                    <div class="row g-0 align-items-center">
                        <div class="col-8">
                            <span class="text-muted small">
                                <i class="bi bi-info-circle me-1"></i> 
                                <?php echo e($module->syllabusItems->filter(fn($i) => $i->realisations->isNotEmpty())->count()); ?> / <?php echo e($module->syllabusItems->count()); ?> chapitres complétés
                            </span>
                        </div>
                        <div class="col-4 text-end">
                            <a href="<?php echo e(route('professeur.emploi')); ?>" class="btn btn-sm btn-link p-0 text-decoration-none fw-bold" style="font-size: 0.8rem;">
                                Valider séance <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12 py-5 text-center">
            <div class="card border-0 shadow-sm py-5 rounded-4">
                <div class="card-body">
                    <i class="bi bi-book fs-1 text-muted opacity-25 d-block mb-3"></i>
                    <h5 class="text-muted">Aucun module assigné n'a été trouvé.</h5>
                    <p class="text-muted small">Veuillez contacter l'administration pour vérifier vos modules.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.professeur', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/professeur/avancement.blade.php ENDPATH**/ ?>