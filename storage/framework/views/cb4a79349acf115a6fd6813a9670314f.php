<?php $__env->startSection('title', 'Mon Tableau de Bord'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h4><i class="bi bi-person-circle me-2"></i>Bienvenue, <?php echo e(auth()->user()->name); ?></h4>
                <?php if($professeur): ?>
                    <p class="mb-0">Spécialité: <?php echo e($professeur->specialite ?? 'Non définie'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if($professeur): ?>
    <?php
        $jourActuel = ucfirst(now()->locale('fr')->isoFormat('dddd'));
        $unvalidatedCount = \App\Models\EmploiDuTemps::where('professeur_id', $professeur->id)
            ->where('jour', $jourActuel)
            ->where('heure_fin', '<', now()->toTimeString())
            ->whereDoesntHave('realisations', function($q) {
                $q->where('date', now()->toDateString());
            })
            ->count();
    ?>

    <?php if($unvalidatedCount > 0): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-0">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <strong>Attention :</strong> Vous avez <strong><?php echo e($unvalidatedCount); ?></strong> séances passées qui ne sont pas encore validées. 
                        <a href="<?php echo e(route('professeur.emploi')); ?>" class="alert-link ms-2">Consulter l'emploi du temps <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Progress Tracking -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 text-primary"><i class="bi bi-graph-up-arrow me-2"></i>Suivi de Progression des Modules</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php
                        $modulesProf = $professeur->modules->load(['syllabusItems.realisations']);
                    ?>
                    <?php $__empty_1 = true; $__currentLoopData = $modulesProf; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                <div class="card-header bg-light border-0 py-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold mb-0 text-dark"><?php echo e($module->nom); ?></h6>
                                        <span class="badge rounded-pill bg-primary px-3"><?php echo e($module->progress_syllabus); ?>%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" 
                                             style="width: <?php echo e($module->progress_syllabus); ?>%" 
                                             aria-valuenow="<?php echo e($module->progress_syllabus); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush small">
                                        <?php $__empty_2 = true; $__currentLoopData = $module->syllabusItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                            <?php $isDone = $item->realisations->isNotEmpty(); ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3 <?php echo e($isDone ? 'bg-success bg-opacity-10' : ''); ?>">
                                                <div class="d-flex align-items-center">
                                                    <?php if($isDone): ?>
                                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-circle text-muted me-2"></i>
                                                    <?php endif; ?>
                                                    <span class="<?php echo e($isDone ? 'text-success fw-medium text-decoration-line-through' : 'text-dark'); ?>">
                                                        <?php echo e($item->titre); ?>

                                                    </span>
                                                </div>
                                                <span class="text-muted" style="font-size: 0.85em;"><?php echo e($item->poids_pourcentage); ?>%</span>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                            <li class="list-group-item text-center text-muted py-4">
                                                <i class="bi bi-info-circle me-1"></i> Aucun chapitre défini
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <div class="card-footer bg-white border-0 py-2 small text-muted">
                                    <i class="bi bi-info-circle me-1"></i> 
                                    <?php echo e($module->syllabusItems->filter(fn($i) => $i->realisations->isNotEmpty())->count()); ?> / <?php echo e($module->syllabusItems->count()); ?> chapitres complétés
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-12 text-center text-muted py-5">
                            <div class="mb-3">
                                <i class="bi bi-book fs-1 opacity-25"></i>
                            </div>
                            Aucun module n'est associé à votre compte.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <h1 class="display-4 text-primary"><?php echo e($totalSeances); ?></h1>
                <p class="text-muted">Séances cette semaine</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <h1 class="display-4 text-success"><?php echo e($seancesAujourdhui->count()); ?></h1>
                <p class="text-muted">Séances aujourd'hui</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <?php
                    $totalHeuresMensuelles = collect($hourStats)->sum('heures_mensuelles');
                ?>
                <h1 class="display-4 text-info"><?php echo e(\App\Models\EmploiDuTemps::formatHeures($totalHeuresMensuelles)); ?></h1>
                <p class="text-muted">Total heures / mois</p>
            </div>
        </div>
    </div>
</div>

<?php if(count($hourStats) > 0): ?>
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Suivi des Heures par Module</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <?php $__currentLoopData = $hourStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6 mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <div>
                            <strong><?php echo e($stat['nom']); ?></strong> 
                            <small class="text-muted">(<?php echo e($stat['code']); ?>)</small>
                        </div>
                        <div>
                            <span class="fw-bold"><?php echo e(\App\Models\EmploiDuTemps::formatHeures($stat['heures_mensuelles'])); ?></span>
                            <?php if($stat['max_heures']): ?>
                                <small class="text-muted">/ <?php echo e(\App\Models\EmploiDuTemps::formatHeures($stat['max_heures'])); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php
                        $pourcentage = 0;
                        if($stat['max_heures'] > 0) {
                            $pourcentage = round(($stat['heures_mensuelles'] / $stat['max_heures']) * 100);
                        }
                        $colorClass = 'bg-success';
                        if($pourcentage >= 90) $colorClass = 'bg-danger';
                        elseif($pourcentage >= 70) $colorClass = 'bg-warning';
                    ?>

                    <div class="progress" style="height: 10px;">
                        <?php if($stat['max_heures']): ?>
                            <div class="progress-bar <?php echo e($colorClass); ?>" role="progressbar" 
                                 style="width: <?php echo e(min($pourcentage, 100)); ?>%" 
                                 aria-valuenow="<?php echo e($pourcentage); ?>" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        <?php else: ?>
                            <div class="progress-bar bg-info" role="progressbar" style="width: 100%"></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-calendar-day me-2"></i>Mes séances d'aujourd'hui</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Horaire</th><th>Groupe</th><th>Module</th><th>Salle</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $seancesAujourdhui; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="<?php echo e($seance->statut_approbation === 'approved' && !$seance->actif ? 'table-secondary opacity-50' : ''); ?>">
                            <td><span class="badge bg-primary"><?php echo e($seance->heure_debut); ?> - <?php echo e($seance->heure_fin); ?></span></td>
                            <td><?php echo e($seance->groupe?->nom ?? '—'); ?></td>
                            <td class="<?php echo e($seance->statut_approbation === 'approved' && !$seance->actif ? 'text-decoration-line-through' : ''); ?>"><?php echo e($seance->module?->nom ?? '—'); ?></td>
                            <td><?php echo e($seance->salle?->nom ?? '—'); ?></td>
                            <td>
                                <?php if($seance->statut_approbation === 'approved' && !$seance->actif): ?>
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i> Annulée</span>
                                <?php elseif($seance->isRealisee()): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Validée</span>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalValider<?php echo e($seance->id); ?>">
                                        <i class="bi bi-check-lg pe-1"></i> Valider
                                    </button>

                                    <!-- Modal Valider -->
                                    <div class="modal fade" id="modalValider<?php echo e($seance->id); ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <form action="<?php echo e(route('professeur.emploi.confirmer', $seance)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title"><i class="bi bi-check2-square me-2"></i>Validation de la séance</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-bold">Session</label>
                                                                <p class="mb-0 text-muted"><?php echo e($seance->heure_debut); ?> - <?php echo e($seance->heure_fin); ?> | <?php echo e($seance->groupe->nom); ?></p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <?php $availableModules = $seance->getModulesDisponibles(); ?>
                                                                <label class="form-label fw-bold">Module enseigné</label>
                                                                <select name="module_id" class="form-select <?php if($availableModules->count() > 1): ?> border-primary <?php endif; ?>" onchange="toggleSyllabus(this, '<?php echo e($seance->id); ?>')">
                                                                    <?php $__currentLoopData = $availableModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <option value="<?php echo e($m->id); ?>" <?php echo e($m->id == $seance->module_id ? 'selected' : ''); ?>>
                                                                            <?php echo e($m->nom); ?>

                                                                        </option>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold"><i class="bi bi-book me-2"></i>Chapitres traités aujourd'hui</label>
                                                            <?php $__currentLoopData = $availableModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <div class="syllabus-container syllabus-module-<?php echo e($m->id); ?>-<?php echo e($seance->id); ?>" style="<?php echo e($m->id == $seance->module_id ? '' : 'display:none;'); ?>">
                                                                    <?php $__empty_2 = true; $__currentLoopData = $m->syllabusItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                                        <div class="form-check form-check-inline border rounded p-2 mb-2 bg-light">
                                                                            <input class="form-check-input ms-0" type="checkbox" name="syllabus_items[]" value="<?php echo e($item->id); ?>" id="item<?php echo e($item->id); ?><?php echo e($seance->id); ?>">
                                                                            <label class="form-check-label ms-1" for="item<?php echo e($item->id); ?><?php echo e($seance->id); ?>">
                                                                                <?php echo e($item->titre); ?> <small class="text-muted">(<?php echo e($item->poids_pourcentage); ?>%)</small>
                                                                            </label>
                                                                        </div>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                                        <p class="text-muted small">Aucun chapitre défini pour ce module.</p>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>

                                                        <hr>
                                                        <h6 class="mb-3"><i class="bi bi-people me-2"></i>Appel / Présences <small class="text-muted">(<?php echo e($seance->groupe->etudiants->count()); ?> stagiaires)</small></h6>
                                                        
                                                        <div class="table-responsive" style="max-height: 300px;">
                                                            <table class="table table-sm table-hover border">
                                                                <thead class="table-light sticky-top">
                                                                    <tr>
                                                                        <th>Stagiaire</th>
                                                                        <th class="text-center">Présent</th>
                                                                        <th class="text-center">Absent</th>
                                                                        <th class="text-center">Justifié</th>
                                                                        <th>Commentaire</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $__currentLoopData = $seance->groupe->etudiants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $etudiant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <tr>
                                                                            <td><?php echo e($etudiant->nom_complet); ?></td>
                                                                            <td class="text-center">
                                                                                <input class="form-check-input" type="radio" name="attendance[<?php echo e($etudiant->id); ?>]" value="Présent" checked>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <input class="form-check-input" type="radio" name="attendance[<?php echo e($etudiant->id); ?>]" value="Absent">
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <input class="form-check-input" type="radio" name="attendance[<?php echo e($etudiant->id); ?>]" value="Justifié">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="commentaire[<?php echo e($etudiant->id); ?>]" class="form-control form-control-sm" placeholder="Oubli, retard...">
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="bi bi-check-lg me-2"></i>Confirmer la présence et valider
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">Aucune séance aujourd'hui</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($seancesAujourdhui->count() > 0): ?>
        <div class="card-footer">
            <a href="<?php echo e(route('professeur.emploi')); ?>" class="btn btn-outline-primary">
                <i class="bi bi-calendar3 me-2"></i> Voir mon emploi du temps complet
            </a>
        </div>
    <?php endif; ?>
</div>
<script>
    function toggleSyllabus(select, seanceId) {
        const moduleId = select.value;
        document.querySelectorAll(`.syllabus-container[class*="-${seanceId}"]`).forEach(el => {
            el.style.display = 'none';
        });
        const activeContainer = document.querySelector(`.syllabus-module-${moduleId}-${seanceId}`);
        if (activeContainer) {
            activeContainer.style.display = 'block';
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.professeur', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/professeur/dashboard.blade.php ENDPATH**/ ?>