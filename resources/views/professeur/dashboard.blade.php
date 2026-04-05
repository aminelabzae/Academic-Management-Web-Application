@extends('layouts.professeur')

@section('title', 'Mon Tableau de Bord')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h4><i class="bi bi-person-circle me-2"></i>Bienvenue, {{ auth()->user()->name }}</h4>
                @if($professeur)
                    <p class="mb-0">Spécialité: {{ $professeur->specialite ?? 'Non définie' }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if($professeur)
    @php
        $jourActuel = ucfirst(now()->locale('fr')->isoFormat('dddd'));
        $unvalidatedCount = \App\Models\EmploiDuTemps::where('professeur_id', $professeur->id)
            ->where('jour', $jourActuel)
            ->where('heure_fin', '<', now()->toTimeString())
            ->whereDoesntHave('realisations', function($q) {
                $q->where('date', now()->toDateString());
            })
            ->count();
    @endphp

    @if($unvalidatedCount > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-0">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <strong>Attention :</strong> Vous avez <strong>{{ $unvalidatedCount }}</strong> séances passées qui ne sont pas encore validées. 
                        <a href="{{ route('professeur.emploi') }}" class="alert-link ms-2">Consulter l'emploi du temps <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

<!-- Progress Tracking -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 text-primary"><i class="bi bi-graph-up-arrow me-2"></i>Suivi de Progression des Modules</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $modulesProf = $professeur->modules->load(['syllabusItems.realisations']);
                    @endphp
                    @forelse($modulesProf as $module)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                <div class="card-header bg-light border-0 py-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold mb-0 text-dark">{{ $module->nom }}</h6>
                                        <span class="badge rounded-pill bg-primary px-3">{{ $module->progress_syllabus }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" 
                                             style="width: {{ $module->progress_syllabus }}%" 
                                             aria-valuenow="{{ $module->progress_syllabus }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush small">
                                        @forelse($module->syllabusItems as $item)
                                            @php $isDone = $item->realisations->isNotEmpty(); @endphp
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3 {{ $isDone ? 'bg-success bg-opacity-10' : '' }}">
                                                <div class="d-flex align-items-center">
                                                    @if($isDone)
                                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                    @else
                                                        <i class="bi bi-circle text-muted me-2"></i>
                                                    @endif
                                                    <span class="{{ $isDone ? 'text-success fw-medium text-decoration-line-through' : 'text-dark' }}">
                                                        {{ $item->titre }}
                                                    </span>
                                                </div>
                                                <span class="text-muted" style="font-size: 0.85em;">{{ $item->poids_pourcentage }}%</span>
                                            </li>
                                        @empty
                                            <li class="list-group-item text-center text-muted py-4">
                                                <i class="bi bi-info-circle me-1"></i> Aucun chapitre défini
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                                <div class="card-footer bg-white border-0 py-2 small text-muted">
                                    <i class="bi bi-info-circle me-1"></i> 
                                    {{ $module->syllabusItems->filter(fn($i) => $i->realisations->isNotEmpty())->count() }} / {{ $module->syllabusItems->count() }} chapitres complétés
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-5">
                            <div class="mb-3">
                                <i class="bi bi-book fs-1 opacity-25"></i>
                            </div>
                            Aucun module n'est associé à votre compte.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <h1 class="display-4 text-primary">{{ $totalSeances }}</h1>
                <p class="text-muted">Séances cette semaine</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <h1 class="display-4 text-success">{{ $seancesAujourdhui->count() }}</h1>
                <p class="text-muted">Séances aujourd'hui</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                @php
                    $totalHeuresMensuelles = collect($hourStats)->sum('heures_mensuelles');
                @endphp
                <h1 class="display-4 text-info">{{ \App\Models\EmploiDuTemps::formatHeures($totalHeuresMensuelles) }}</h1>
                <p class="text-muted">Total heures / mois</p>
            </div>
        </div>
    </div>
</div>

@if(count($hourStats) > 0)
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Suivi des Heures par Module</h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($hourStats as $stat)
                <div class="col-md-6 mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <div>
                            <strong>{{ $stat['nom'] }}</strong> 
                            <small class="text-muted">({{ $stat['code'] }})</small>
                        </div>
                        <div>
                            <span class="fw-bold">{{ \App\Models\EmploiDuTemps::formatHeures($stat['heures_mensuelles']) }}</span>
                            @if($stat['max_heures'])
                                <small class="text-muted">/ {{ \App\Models\EmploiDuTemps::formatHeures($stat['max_heures']) }}</small>
                            @endif
                        </div>
                    </div>
                    
                    @php
                        $pourcentage = 0;
                        if($stat['max_heures'] > 0) {
                            $pourcentage = round(($stat['heures_mensuelles'] / $stat['max_heures']) * 100);
                        }
                        $colorClass = 'bg-success';
                        if($pourcentage >= 90) $colorClass = 'bg-danger';
                        elseif($pourcentage >= 70) $colorClass = 'bg-warning';
                    @endphp

                    <div class="progress" style="height: 10px;">
                        @if($stat['max_heures'])
                            <div class="progress-bar {{ $colorClass }}" role="progressbar" 
                                 style="width: {{ min($pourcentage, 100) }}%" 
                                 aria-valuenow="{{ $pourcentage }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        @else
                            <div class="progress-bar bg-info" role="progressbar" style="width: 100%"></div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

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
                    @forelse($seancesAujourdhui as $seance)
                        <tr class="{{ $seance->statut_approbation === 'approved' && !$seance->actif ? 'table-secondary opacity-50' : '' }}">
                            <td><span class="badge bg-primary">{{ $seance->heure_debut }} - {{ $seance->heure_fin }}</span></td>
                            <td>{{ $seance->groupe?->nom ?? '—' }}</td>
                            <td class="{{ $seance->statut_approbation === 'approved' && !$seance->actif ? 'text-decoration-line-through' : '' }}">{{ $seance->module?->nom ?? '—' }}</td>
                            <td>{{ $seance->salle?->nom ?? '—' }}</td>
                            <td>
                                @if($seance->statut_approbation === 'approved' && !$seance->actif)
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i> Annulée</span>
                                @elseif($seance->isRealisee())
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Validée</span>
                                @else
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalValider{{ $seance->id }}">
                                        <i class="bi bi-check-lg pe-1"></i> Valider
                                    </button>

                                    <!-- Modal Valider -->
                                    <div class="modal fade" id="modalValider{{ $seance->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <form action="{{ route('professeur.emploi.confirmer', $seance) }}" method="POST">
                                                @csrf
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title"><i class="bi bi-check2-square me-2"></i>Validation de la séance</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-bold">Session</label>
                                                                <p class="mb-0 text-muted">{{ $seance->heure_debut }} - {{ $seance->heure_fin }} | {{ $seance->groupe->nom }}</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                @php $availableModules = $seance->getModulesDisponibles(); @endphp
                                                                <label class="form-label fw-bold">Module enseigné</label>
                                                                <select name="module_id" class="form-select @if($availableModules->count() > 1) border-primary @endif" onchange="toggleSyllabus(this, '{{ $seance->id }}')">
                                                                    @foreach($availableModules as $m)
                                                                        <option value="{{ $m->id }}" {{ $m->id == $seance->module_id ? 'selected' : '' }}>
                                                                            {{ $m->nom }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold"><i class="bi bi-book me-2"></i>Chapitres traités aujourd'hui</label>
                                                            @foreach($availableModules as $m)
                                                                <div class="syllabus-container syllabus-module-{{ $m->id }}-{{ $seance->id }}" style="{{ $m->id == $seance->module_id ? '' : 'display:none;' }}">
                                                                    @forelse($m->syllabusItems as $item)
                                                                        <div class="form-check form-check-inline border rounded p-2 mb-2 bg-light">
                                                                            <input class="form-check-input ms-0" type="checkbox" name="syllabus_items[]" value="{{ $item->id }}" id="item{{ $item->id }}{{ $seance->id }}">
                                                                            <label class="form-check-label ms-1" for="item{{ $item->id }}{{ $seance->id }}">
                                                                                {{ $item->titre }} <small class="text-muted">({{ $item->poids_pourcentage }}%)</small>
                                                                            </label>
                                                                        </div>
                                                                    @empty
                                                                        <p class="text-muted small">Aucun chapitre défini pour ce module.</p>
                                                                    @endforelse
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <hr>
                                                        <h6 class="mb-3"><i class="bi bi-people me-2"></i>Appel / Présences <small class="text-muted">({{ $seance->groupe->etudiants->count() }} stagiaires)</small></h6>
                                                        
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
                                                                    @foreach($seance->groupe->etudiants as $etudiant)
                                                                        <tr>
                                                                            <td>{{ $etudiant->nom_complet }}</td>
                                                                            <td class="text-center">
                                                                                <input class="form-check-input" type="radio" name="attendance[{{ $etudiant->id }}]" value="Présent" checked>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <input class="form-check-input" type="radio" name="attendance[{{ $etudiant->id }}]" value="Absent">
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <input class="form-check-input" type="radio" name="attendance[{{ $etudiant->id }}]" value="Justifié">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="commentaire[{{ $etudiant->id }}]" class="form-control form-control-sm" placeholder="Oubli, retard...">
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
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
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Aucune séance aujourd'hui</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($seancesAujourdhui->count() > 0)
        <div class="card-footer">
            <a href="{{ route('professeur.emploi') }}" class="btn btn-outline-primary">
                <i class="bi bi-calendar3 me-2"></i> Voir mon emploi du temps complet
            </a>
        </div>
    @endif
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
@endsection
