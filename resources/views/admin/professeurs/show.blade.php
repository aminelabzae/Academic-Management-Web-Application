?@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Détails du Professeur</h4>
                    <a href="{{ route('admin.professeurs.index') }}" class="btn btn-primary">Retour</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Matricule:</strong> {{ $professeur->matricule }}</p>
                            <p><strong>Nom:</strong> {{ $professeur->nom }}</p>
                            <p><strong>Prénom:</strong> {{ $professeur->prenom }}</p>
                            <p><strong>Email:</strong> {{ $professeur->email }}</p>
                            <p><strong>Téléphone:</strong> {{ $professeur->telephone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Spécialité:</strong> {{ $professeur->specialite }}</p>
                            <p><strong>Créé le:</strong> {{ $professeur->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Modifié le:</strong> {{ $professeur->updated_at->format('d/m/Y H:i') }}</p>

                            @php
                                $heuresHebdo = $professeur->getHeuresHebdomadairesActuelles();
                                $heuresMensuelles = $heuresHebdo; // Synchronisation
                                $maxHeures = $professeur->max_heures_mensuel;
                                $pourcentage = $maxHeures ? round(($heuresMensuelles / $maxHeures) * 100) : 0;
                            @endphp

                            <p><strong><i class="bi bi-clock me-1"></i>Charge Hebdomadaire:</strong>
                                <span class="badge bg-primary fs-6">{{ \App\Models\EmploiDuTemps::formatHeures($heuresHebdo) }} / mois</span>
                            </p>



                            @php
                                $heuresRealisees = $professeur->getHeuresMensuellesRealisees();
                            @endphp
                            <p><strong><i class="bi bi-check-circle me-1"></i>Heures Validées ce mois:</strong>
                                <span class="badge bg-success">{{ \App\Models\EmploiDuTemps::formatHeures($heuresRealisees) }}</span>
                            </p>

                            @if($maxHeures)
                                <label class="small text-muted mb-1">Consommation de la limite mensuelle ({{ $maxHeures }}h)</label>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div class="progress-bar {{ $pourcentage >= 90 ? 'bg-danger' : ($pourcentage >= 70 ? 'bg-warning' : 'bg-success') }}"
                                         role="progressbar" style="width: {{ min($pourcentage, 100) }}%">
                                        {{ \App\Models\EmploiDuTemps::formatHeures($heuresMensuelles) }} / {{ $maxHeures }}h ({{ $pourcentage }}%)
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



