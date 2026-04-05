

<?php $__env->startSection('title', 'Mon Emploi du Temps'); ?>

<?php $__env->startSection('content'); ?>
<?php if($professeur): ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Mon emploi du temps</h5>
        <span class="badge bg-info"><?php echo e($professeur->nom_complet); ?></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="100">Horaire</th>
                        <?php $__currentLoopData = $jours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th class="text-center"><?php echo e($jour); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $creneaux; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $heureDebut => $heureFin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="align-middle text-center bg-light">
                                <strong><?php echo e($heureDebut); ?></strong><br>
                                <small class="text-muted"><?php echo e($heureFin); ?></small>
                            </td>
                            <?php $__currentLoopData = $jours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $seance = isset($emplois[$jour])
                                        ? $emplois[$jour]->first(function($s) use ($heureDebut) {
                                            return substr($s->heure_debut, 0, 5) == $heureDebut;
                                        })
                                        : null;
                                ?>
                                <?php
                                    $isAnnulee = $seance && !$seance->actif && $seance->statut_approbation === 'approved';
                                ?>
                                <td class="p-2 <?php echo e($seance ? ($isAnnulee ? 'bg-secondary bg-opacity-10' : ($seance->is_examen ? 'bg-danger bg-opacity-10' : 'bg-success bg-opacity-10')) : ''); ?>">
                                    <?php if($seance): ?>
                                        <div class="p-2 rounded bg-white shadow-sm border-start border-4 <?php echo e($isAnnulee ? 'border-secondary opacity-50' : ($seance->is_examen ? 'border-danger' : 'border-success')); ?>">
                                            <?php if($isAnnulee): ?>
                                                <span class="badge bg-secondary mb-1">ANNULÉE</span>
                                            <?php elseif($seance->is_examen): ?>
                                                <span class="badge bg-danger mb-1">EXAMEN</span>
                                            <?php endif; ?>
                                            <strong class="d-block <?php echo e($isAnnulee ? 'text-secondary text-decoration-line-through' : ($seance->is_examen ? 'text-danger' : 'text-success')); ?>"><?php echo e($seance->module->nom); ?></strong>
                                            <small class="d-block text-muted">
                                                <i class="bi bi-people me-1"></i><?php echo e($seance->groupe->nom); ?>

                                            </small>
                                            <small class="d-block text-muted">
                                                <?php if($seance->type_seance === 'Teams'): ?>
                                                    <span class="badge bg-info p-1"><i class="bi bi-laptop me-1"></i>Teams</span>
                                                <?php else: ?>
                                                    <i class="bi bi-building me-1"></i><?php echo e($seance->salle->nom); ?>

                                                <?php endif; ?>
                                            </small>

                                            <div class="mt-2 d-flex flex-wrap gap-1">
                                                <?php if(!$isAnnulee): ?>
                                                    <form action="<?php echo e(route('professeur.emploi.toggle-examen', $seance->id)); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-sm <?php echo e($seance->is_examen ? 'btn-danger' : 'btn-outline-danger'); ?> p-1" title="Marquer comme Examen">
                                                            <i class="bi bi-file-earmark-text"></i>
                                                        </button>
                                                    </form>
                                                <?php
                                                    $isAujourdhui = $jour === ucfirst(now()->locale('fr')->isoFormat('dddd'));
                                                ?>

                                                <?php if($isAujourdhui): ?>
                                                    <?php if($seance->isRealisee()): ?>
                                                        <span class="badge bg-success p-1" title="Séance Validée">
                                                            <i class="bi bi-check-circle"></i>
                                                        </span>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-success p-1" 
                                                                data-bs-toggle="modal" data-bs-target="#modalValiderEmploi<?php echo e($seance->id); ?>" title="Valider la séance">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>

                                                        <!-- Modal Valider (Unified) -->
                                                        <div class="modal fade" id="modalValiderEmploi<?php echo e($seance->id); ?>" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg text-start" style="text-align: left !important;">
                                                                <form action="<?php echo e(route('professeur.emploi.confirmer', $seance)); ?>" method="POST">
                                                                    <?php echo csrf_field(); ?>
                                                                    <div class="modal-content text-dark">
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
                                                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                        <label class="form-label fw-bold small text-start mb-0"><i class="bi bi-book me-2"></i>Chapitres traités aujourd'hui</label>
                                                                                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" data-bs-toggle="collapse" data-bs-target="#collapseAddSyllabus<?php echo e($seance->id); ?>">
                                                                                            <i class="bi bi-plus"></i> Nouveau chapitre
                                                                                        </button>
                                                                                    </div>
                                                                                    
                                                                                    <!-- Formulaire d'ajout rapide de chapitre -->
                                                                                    <div class="collapse mb-3" id="collapseAddSyllabus<?php echo e($seance->id); ?>">
                                                                                        <div class="card card-body bg-light border-primary border-opacity-25 p-2 shadow-sm">
                                                                                            <div class="row align-items-end g-2">
                                                                                                <div class="col-md-7">
                                                                                                    <label class="form-label small text-muted mb-0">Titre du chapitre</label>
                                                                                                    <input type="text" id="newSyllabusTitle<?php echo e($seance->id); ?>" class="form-control form-control-sm" placeholder="Ex: Introduction aux bases">
                                                                                                </div>
                                                                                                <div class="col-md-3">
                                                                                                    <label class="form-label small text-muted mb-0">Poids (%)</label>
                                                                                                    <input type="number" id="newSyllabusWeight<?php echo e($seance->id); ?>" class="form-control form-control-sm" min="0" max="100" value="10">
                                                                                                </div>
                                                                                                <div class="col-md-2 text-end">
                                                                                                    <button type="button" class="btn btn-sm btn-primary w-100" onclick="addSyllabusItem('<?php echo e($seance->id); ?>')">
                                                                                                        <i class="bi bi-check2"></i>
                                                                                                    </button>
                                                                                                </div>
                                                                                            </div>
                                                                                            <small id="syllabusError<?php echo e($seance->id); ?>" class="text-danger mt-1 d-none"></small>
                                                                                        </div>
                                                                                    </div>

                                                                                <?php $__currentLoopData = $availableModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                    <div class="syllabus-container syllabus-module-<?php echo e($m->id); ?>-<?php echo e($seance->id); ?>" style="<?php echo e($m->id == $seance->module_id ? '' : 'display:none;'); ?>">
                                                                                        <div id="syllabusList<?php echo e($m->id); ?>-<?php echo e($seance->id); ?>">
                                                                                        <?php $__empty_1 = true; $__currentLoopData = $m->syllabusItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                                                            <div class="form-check form-check-inline border rounded p-1 mb-1 bg-light small">
                                                                                                <input class="form-check-input ms-0" type="checkbox" name="syllabus_items[]" value="<?php echo e($item->id); ?>" id="item<?php echo e($item->id); ?><?php echo e($seance->id); ?>">
                                                                                                <label class="form-check-label ms-1" for="item<?php echo e($item->id); ?><?php echo e($seance->id); ?>">
                                                                                                    <?php echo e($item->titre); ?> <small class="text-muted">(<?php echo e($item->poids_pourcentage); ?>%)</small>
                                                                                                </label>
                                                                                            </div>
                                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                                                            <p class="text-muted small empty-syllabus-msg">Aucun syllabus défini.</p>
                                                                                        <?php endif; ?>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                </div>

                                                                            <hr>
                                                                            <h6 class="mb-3"><i class="bi bi-people me-2"></i>Appel / Présences <small class="text-muted">(<?php echo e($seance->groupe->etudiants?->count() ?? 0); ?> stagiaires)</small></h6>
                                                                            
                                                                            <div class="table-responsive" style="max-height: 300px;">
                                                                                <table class="table table-sm table-hover border">
                                                                                    <thead class="table-light sticky-top">
                                                                                        <tr>
                                                                                            <th>Stagiaire</th>
                                                                                            <th class="text-center">P</th>
                                                                                            <th class="text-center">A</th>
                                                                                            <th class="text-center">J</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <?php if($seance->groupe->etudiants && $seance->groupe->etudiants->count() > 0): ?>
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
                                                                                                </tr>
                                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                        <?php else: ?>
                                                                                            <tr><td colspan="4" class="text-center">Aucun stagiaire répertorié</td></tr>
                                                                                        <?php endif; ?>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer bg-light">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                            <button type="submit" class="btn btn-success">
                                                                                <i class="bi bi-check-lg me-2"></i>Valider
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <?php if($seance->type_seance === 'Teams'): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-info p-1" 
                                                            data-bs-toggle="modal" data-bs-target="#linkModal<?php echo e($seance->id); ?>" title="Lien de la séance">
                                                        <i class="bi bi-link-45deg"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if($seance->statut_approbation === 'approved' && $seance->actif): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-warning p-1" 
                                                            data-bs-toggle="modal" data-bs-target="#cancelModal<?php echo e($seance->id); ?>" title="Demander l'annulation">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>

                                                    <!-- Modal Annulation -->
                                                    <div class="modal fade" id="cancelModal<?php echo e($seance->id); ?>" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content text-start">
                                                                <form action="<?php echo e(route('professeur.emploi.cancel', $seance->id)); ?>" method="POST">
                                                                    <?php echo csrf_field(); ?>
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Demander l'annulation</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Motif de l'annulation pour <strong><?php echo e($seance->module->nom); ?></strong> :</p>
                                                                        <textarea name="reason" class="form-control" rows="3" required placeholder="Raison de l'annulation (ex: Maladie, Absence prévue)..."></textarea>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                        <button type="submit" class="btn btn-warning">Envoyer la demande</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php elseif($seance->statut_approbation === 'pending'): ?>
                                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>En attente</span>
                                                <?php elseif($seance->statut_approbation === 'rejected'): ?>
                                                    <span class="badge bg-danger"><i class="bi bi-x-octagon me-1"></i>Refusé</span>
                                                <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Votre profil professeur n'est pas encore configuré. Veuillez contacter l'administrateur.
</div>
<?php endif; ?>
<script>
    function toggleSyllabus(select, seanceId) {
        const moduleId = select.value;
        // Masquer tous les containers de cette séance
        document.querySelectorAll(`.syllabus-container[class*="-${seanceId}"]`).forEach(el => {
            el.style.display = 'none';
        });
        // Afficher celui du module sélectionné
        const activeContainer = document.querySelector(`.syllabus-module-${moduleId}-${seanceId}`);
        if (activeContainer) {
            activeContainer.style.display = 'block';
        }
    }

    function addSyllabusItem(seanceId) {
        // Trouver le form contenant ce seanceId pour récupérer le module_id actuellement sélectionné
        const modal = document.getElementById('modalValiderEmploi' + seanceId);
        const moduleId = modal.querySelector('select[name="module_id"]').value;
        
        const titleInput = document.getElementById('newSyllabusTitle' + seanceId);
        const weightInput = document.getElementById('newSyllabusWeight' + seanceId);
        const errorSpan = document.getElementById('syllabusError' + seanceId);
        
        const titre = titleInput.value.trim();
        const poids = weightInput.value;

        if (!titre) {
            errorSpan.textContent = "Le titre est requis.";
            errorSpan.classList.remove('d-none');
            return;
        }

        // Requête AJAX
        fetch(`/professeur/modules/${moduleId}/syllabus`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ titre: titre, poids_pourcentage: poids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Créer le nouvel élément HTML pour la checkbox
                const listContainer = document.getElementById('syllabusList' + moduleId + '-' + seanceId);
                
                // Supprimer le message "Aucun syllabus défini" s'il existe
                const emptyMsg = listContainer.querySelector('.empty-syllabus-msg');
                if (emptyMsg) emptyMsg.remove();

                const newItemHtml = `
                    <div class="form-check form-check-inline border rounded p-1 mb-1 bg-light small border-success">
                        <input class="form-check-input ms-0" type="checkbox" name="syllabus_items[]" value="${data.item.id}" id="item${data.item.id}${seanceId}" checked>
                        <label class="form-check-label ms-1" for="item${data.item.id}${seanceId}">
                            ${data.item.titre} <small class="text-muted">(${data.item.poids_pourcentage}%)</small>
                            <span class="badge bg-success ms-1" style="font-size: 0.6em;">Nouveau</span>
                        </label>
                    </div>
                `;
                
                listContainer.insertAdjacentHTML('beforeend', newItemHtml);
                
                // Réinitialiser le formulaire
                titleInput.value = '';
                weightInput.value = '10';
                errorSpan.classList.add('d-none');
                
                // Fermer le collapse
                const collapseElement = document.getElementById('collapseAddSyllabus' + seanceId);
                const bsCollapse = bootstrap.Collapse.getInstance(collapseElement);
                if (bsCollapse) bsCollapse.hide();
                
            } else {
                errorSpan.textContent = data.error || "Erreur lors de l'ajout.";
                errorSpan.classList.remove('d-none');
            }
        })
        .catch(error => {
            errorSpan.textContent = "Erreur de connexion.";
            errorSpan.classList.remove('d-none');
        });
    }
</script>
<?php $__env->stopSection(); ?>


<?php $__currentLoopData = $emplois; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jourSeances): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $jourSeances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <!-- Modal Lien Teams -->
        <?php if($seance->type_seance === 'Teams'): ?>
            <div class="modal fade" id="linkModal<?php echo e($seance->id); ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="<?php echo e(route('professeur.emploi.update-link', $seance->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="modal-header">
                                <h5 class="modal-title">Lien de la séance (Teams)</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Collez le lien de la réunion pour <strong><?php echo e($seance->module->nom); ?></strong> :</p>
                                <input type="url" name="teams_link" class="form-control" 
                                       value="<?php echo e($seance->teams_link); ?>" 
                                       placeholder="https://teams.microsoft.com/l/meetup-join/...">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Enregistrer le lien</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($seance->statut_approbation === 'approved'): ?>
            <!-- Modal Annulation -->
            <div class="modal fade" id="cancelModal<?php echo e($seance->id); ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="<?php echo e(route('professeur.emploi.cancel', $seance->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="modal-header">
                                <h5 class="modal-title">Demander l'annulation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Motif de l'annulation pour <strong><?php echo e($seance->module->nom); ?></strong> :</p>
                                <textarea name="reason" class="form-control" rows="3" required placeholder="Raison de l'annulation (ex: Maladie, Absence prévue)..."></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-warning">Envoyer la demande</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



<?php echo $__env->make('layouts.professeur', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/professeur/emploi.blade.php ENDPATH**/ ?>