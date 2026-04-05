?

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Détails du Professeur</h4>
                    <a href="<?php echo e(route('admin.professeurs.index')); ?>" class="btn btn-primary">Retour</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Matricule:</strong> <?php echo e($professeur->matricule); ?></p>
                            <p><strong>Nom:</strong> <?php echo e($professeur->nom); ?></p>
                            <p><strong>Prénom:</strong> <?php echo e($professeur->prenom); ?></p>
                            <p><strong>Email:</strong> <?php echo e($professeur->email); ?></p>
                            <p><strong>Téléphone:</strong> <?php echo e($professeur->telephone); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Spécialité:</strong> <?php echo e($professeur->specialite); ?></p>
                            <p><strong>Créé le:</strong> <?php echo e($professeur->created_at->format('d/m/Y H:i')); ?></p>
                            <p><strong>Modifié le:</strong> <?php echo e($professeur->updated_at->format('d/m/Y H:i')); ?></p>

                            <?php
                                $heuresHebdo = $professeur->getHeuresHebdomadairesActuelles();
                                $heuresMensuelles = $heuresHebdo; // Synchronisation
                                $maxHeures = $professeur->max_heures_mensuel;
                                $pourcentage = $maxHeures ? round(($heuresMensuelles / $maxHeures) * 100) : 0;
                            ?>

                            <p><strong><i class="bi bi-clock me-1"></i>Charge Hebdomadaire:</strong>
                                <span class="badge bg-primary fs-6"><?php echo e(\App\Models\EmploiDuTemps::formatHeures($heuresHebdo)); ?> / mois</span>
                            </p>



                            <?php
                                $heuresRealisees = $professeur->getHeuresMensuellesRealisees();
                            ?>
                            <p><strong><i class="bi bi-check-circle me-1"></i>Heures Validées ce mois:</strong>
                                <span class="badge bg-success"><?php echo e(\App\Models\EmploiDuTemps::formatHeures($heuresRealisees)); ?></span>
                            </p>

                            <?php if($maxHeures): ?>
                                <label class="small text-muted mb-1">Consommation de la limite mensuelle (<?php echo e($maxHeures); ?>h)</label>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div class="progress-bar <?php echo e($pourcentage >= 90 ? 'bg-danger' : ($pourcentage >= 70 ? 'bg-warning' : 'bg-success')); ?>"
                                         role="progressbar" style="width: <?php echo e(min($pourcentage, 100)); ?>%">
                                        <?php echo e(\App\Models\EmploiDuTemps::formatHeures($heuresMensuelles)); ?> / <?php echo e($maxHeures); ?>h (<?php echo e($pourcentage); ?>%)
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/admin/professeurs/show.blade.php ENDPATH**/ ?>