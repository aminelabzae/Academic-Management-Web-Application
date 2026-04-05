@extends('layouts.admin')

@section('title', 'Planning Hebdomadaire Global')
@section('subtitle', 'Format Matriciel Professionnel')

@section('actions')
    <div class="dropdown d-inline-block">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-printer me-1"></i> PDF Matrix
        </button>
        <ul class="dropdown-menu shadow border-0">
            @php $q = request()->all(); @endphp
            <li><a class="dropdown-item" href="{{ route('admin.emplois.global-pdf', array_merge($q)) }}">Toutes les années</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="{{ route('admin.emplois.global-pdf', array_merge($q, ['annee' => 1])) }}">1ère Année</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.emplois.global-pdf', array_merge($q, ['annee' => 2])) }}">2ème Année</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.emplois.global-pdf', array_merge($q, ['annee' => 3])) }}">3ème Année</a></li>
        </ul>
    </div>
    <a href="{{ route('admin.emplois.create') }}" class="btn btn-sm btn-primary ms-2">
        <i class="bi bi-plus-lg me-1"></i> Nouvelle séance
    </a>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <ul class="nav nav-pills bg-white p-1 rounded shadow-sm d-inline-flex border">
            <li class="nav-item">
                <a class="nav-link py-1 px-3 {{ !$selectedAnnee ? 'active' : '' }}" href="{{ route('admin.emplois.grille-semaine') }}">Tous</a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1 px-3 {{ $selectedAnnee == 1 ? 'active' : '' }}" href="{{ route('admin.emplois.grille-semaine', ['annee' => 1]) }}">Année 1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1 px-3 {{ $selectedAnnee == 2 ? 'active' : '' }}" href="{{ route('admin.emplois.grille-semaine', ['annee' => 2]) }}">Année 2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1 px-3 {{ $selectedAnnee == 3 ? 'active' : '' }}" href="{{ route('admin.emplois.grille-semaine', ['annee' => 3]) }}">Année 3</a>
            </li>
            <li class="nav-item ms-3 border-start ps-3 d-flex align-items-center">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="gridRamadanToggle" {{ $isRamadan ? 'checked' : '' }} 
                           onchange="window.location.href='{{ route('admin.emplois.grille-semaine', array_merge(request()->except('ramadan'), ['ramadan' => $isRamadan ? 0 : 1])) }}'">
                    <label class="form-check-label small fw-bold" for="gridRamadanToggle">Mode Ramadan</label>
                </div>
            </li>
        </ul>
    </div>
</div>

<div class="matrix-container shadow-lg bg-white rounded overflow-auto">
    <table class="matrix-table">
        <thead>
            <!-- Header Row 1: Days -->
            <tr>
                <th rowspan="2" class="group-header">GROUPE</th>
                @foreach($jours as $jour)
                    @php 
                        $slots = \App\Models\EmploiDuTemps::getCreneaux($jour, $isRamadan);
                        $count = count($slots);
                    @endphp
                    <th colspan="{{ $count }}" class="day-header header-{{ strtolower($jour) }}">
                        {{ strtoupper($jour) }}
                    </th>
                @endforeach
            </tr>
            <!-- Header Row 2: Time Slots -->
            <tr>
                @foreach($jours as $jour)
                    @php $slots = \App\Models\EmploiDuTemps::getCreneaux($jour, $isRamadan); @endphp
                    @foreach($slots as $debut => $fin)
                        <th class="slot-header">
                            <div>{{ substr($debut, 0, 5) }}</div>
                            <div class="small fw-normal">{{ substr($fin, 0, 5) }}</div>
                        </th>
                    @endforeach
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($groupes as $groupe)
                <tr>
                    <td class="group-cell">
                        <div class="rotated-text">{{ $groupe->nom }}</div>
                    </td>
                    @foreach($jours as $jour)
                        @php 
                            $slots = \App\Models\EmploiDuTemps::getCreneaux($jour, $isRamadan);
                            $slotCount = count($slots);
                        @endphp
                        @for($i = 0; $i < $slotCount; $i++)
                            @php $seance = $emplois[$groupe->id][$jour][$i] ?? null; @endphp
                            <td class="matrix-cell {{ !$seance ? 'empty-cell' : '' }} {{ $seance?->type_seance == 'Teams' ? 'teams-cell' : '' }} {{ $seance?->is_examen ? 'examen-cell' : '' }}">
                                @if($seance)
                                    <div class="seance-content" title="{{ $seance->module->nom }}">
                                        <div class="module-code">{{ $seance->module->code }}</div>
                                        <div class="prof-name">{{ $seance->professeur->nom }}</div>
                                        <div class="salle-name">
                                            @if($seance->type_seance == 'Teams')
                                                <i class="bi bi-laptop"></i> TEAMS
                                            @else
                                                <i class="bi bi-geo-alt"></i> {{ $seance->salle?->nom }}
                                            @endif
                                        </div>
                                        <div class="cell-actions d-flex gap-1 bg-white rounded p-1 shadow-sm">
                                            <button type="button" 
                                               class="btn btn-link p-0 m-0 align-baseline text-primary border-0 open-modal-edit-btn" 
                                               title="Modifier"
                                               data-seance-id="{{ $seance->id }}"
                                               data-groupe-id="{{ $seance->groupe_id }}"
                                               data-module-id="{{ $seance->module_id }}"
                                               data-professeur-id="{{ $seance->professeur_id }}"
                                               data-type-seance="{{ $seance->type_seance }}"
                                               data-salle-id="{{ $seance->salle_id }}"
                                               data-date-debut="{{ $seance->date_debut_validite }}"
                                               data-date-fin="{{ $seance->date_fin_validite }}"
                                               data-heure-debut="{{ substr($seance->heure_debut, 0, 5) }}"
                                               data-heure-fin="{{ substr($seance->heure_fin, 0, 5) }}"
                                               data-actif="{{ $seance->actif ? '1' : '0' }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('admin.emplois.destroy', $seance->id) }}" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette séance ?');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="from_grille" value="1">
                                                <button type="submit" class="btn btn-link p-0 m-0 align-baseline text-danger border-0" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <div class="empty-cell-actions w-100 h-100 d-flex align-items-center justify-content-center">
                                        @php
                                            $keys = array_keys($slots);
                                            $slotDebut = $keys[$i] ?? '08:00';
                                            $slotFin = $slots[$slotDebut] ?? '10:00';
                                        @endphp
                                        <button type="button" 
                                           class="open-modal-add-btn btn btn-sm text-white border-0 shadow-none hover-add-btn" 
                                           title="Ajouter une séance"
                                           data-groupe-id="{{ $groupe->id }}"
                                           data-jour="{{ $jour }}"
                                           data-heure-debut="{{ substr($slotDebut, 0, 5) }}"
                                           data-heure-fin="{{ substr($slotFin, 0, 5) }}">
                                            <i class="bi bi-plus-lg fs-5"></i>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        @endfor
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Seance Modal -->
<div class="modal fade" id="addSeanceModal" tabindex="-1" aria-labelledby="addSeanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSeanceModalLabel"><i class="bi bi-calendar-event me-2"></i><span id="modal_title_text">Ajouter une nouvelle séance</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.emplois.store') }}" method="POST" id="addSeanceForm">
                    @csrf
                    <input type="hidden" name="_method" id="modal_method" value="POST" disabled>
                    <input type="hidden" name="from_grille" value="1">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-check form-switch p-3 bg-light rounded border">
                                <input class="form-check-input ms-0" type="checkbox" id="modal_is_ramadan" name="is_ramadan" value="1" {{ $isRamadan ? 'checked' : '' }}>
                                <label class="form-check-label ms-2 fw-bold" for="modal_is_ramadan">
                                    <i class="bi bi-moon-stars-fill text-primary me-1"></i> Mode Ramadan
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 d-none">
                            <label class="form-label">Jour</label>
                            <input type="hidden" name="jour" id="modal_jour">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date début <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="modal_date_debut_validite" name="date_debut_validite" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Heure début <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_heure_debut" name="heure_debut" required></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Heure fin <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_heure_fin" name="heure_fin" required></select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Groupe <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_groupe_id" name="groupe_id" required>
                                <option value="">Sélectionner un groupe</option>
                                @foreach($groupes as $g)
                                    <option value="{{ $g->id }}">{{ $g->nom }} - {{ $g->filiere->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Module <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_module_id" name="module_id" required>
                                <option value="">Sélectionner un groupe d'abord</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Professeur <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_professeur_id" name="professeur_id" required>
                                <option value="">Sélectionner un groupe d'abord</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_type_seance" name="type_seance" required>
                                <option value="Présentiel">Présentiel</option>
                                <option value="Teams">Teams</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="modal_salle_container">
                            <label class="form-label">Salle <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_salle_id" name="salle_id">
                                <option value="">Choisir</option>
                                @foreach($salles as $salle)
                                    <option value="{{ $salle->id }}">{{ $salle->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12 d-flex align-items-end mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="actif" value="1" id="modal_actif" checked>
                                <label class="form-check-label" for="modal_actif">Séance active</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('modal_type_seance');
    const salleContainer = document.getElementById('modal_salle_container');
    const salleSelect = document.getElementById('modal_salle_id');

    function toggleSalle() {
        if (typeSelect.value === 'Teams') {
            salleContainer.classList.add('d-none');
            salleSelect.required = false;
            salleSelect.value = '';
        } else {
            salleContainer.classList.remove('d-none');
            salleSelect.required = true;
        }
    }
    typeSelect.addEventListener('change', toggleSalle);
    toggleSalle();

    // Slots & Dates
    const slots = @json($allSlots);
    const dateDebutInput = document.getElementById('modal_date_debut_validite');
    const jourInput = document.getElementById('modal_jour');
    const ramadanCheck = document.getElementById('modal_is_ramadan');
    const debutSelect = document.getElementById('modal_heure_debut');
    const finSelect = document.getElementById('modal_heure_fin');
    
    let forcedDebut = null;
    let forcedFin = null;

    function getFrenchDay(dateString) {
        if (!dateString) return '';
        const days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        return days[new Date(dateString).getDay()];
    }

    function updateSlots() {
        if (!dateDebutInput.value) return;
        
        const jour = getFrenchDay(dateDebutInput.value);
        jourInput.value = jour;
        const isRamadan = ramadanCheck.checked;
        
        let currentSlots = isRamadan ? 
            (jour === 'Vendredi' ? slots.ramadan_friday : slots.ramadan_standard) :
            (jour === 'Vendredi' ? slots.friday : slots.standard);

        const oldDebut = forcedDebut || debutSelect.value;
        const oldFin = forcedFin || finSelect.value;
        forcedDebut = null;
        forcedFin = null;

        debutSelect.innerHTML = '<option value="">Début</option>';
        finSelect.innerHTML = '<option value="">Fin</option>';

        for (const [debut, fin] of Object.entries(currentSlots)) {
            const dOpt = document.createElement('option');
            dOpt.value = debut; dOpt.textContent = debut;
            if (debut === oldDebut) dOpt.selected = true;
            debutSelect.appendChild(dOpt);

            const fOpt = document.createElement('option');
            fOpt.value = fin; fOpt.textContent = fin;
            if (fin === oldFin) fOpt.selected = true;
            finSelect.appendChild(fOpt);
        }
    }

    dateDebutInput.addEventListener('change', updateSlots);
    ramadanCheck.addEventListener('change', updateSlots);

    const groupeSelect = document.getElementById('modal_groupe_id');
    const moduleSelect = document.getElementById('modal_module_id');
    const profSelect = document.getElementById('modal_professeur_id');

    async function refreshFilteredData(groupeId, selectedModuleId = null, selectedProfId = null, updateModules = true) {
        if (updateModules) moduleSelect.innerHTML = '<option value="">Chargement...</option>';
        profSelect.innerHTML = '<option value="">Chargement...</option>';
        if (!groupeId) return;

        try {
            let url = `{{ route('admin.emplois.filter-data') }}?groupe_id=${groupeId}`;
            if (selectedModuleId) url += `&module_id=${selectedModuleId}`;

            const response = await fetch(url);
            const data = await response.json();

            if (updateModules) {
                moduleSelect.innerHTML = '<option value="">Sélectionner un module</option>';
                data.modules.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id; opt.textContent = `${m.code} - ${m.nom}`;
                    if (m.id == selectedModuleId) opt.selected = true;
                    moduleSelect.appendChild(opt);
                });
            }

            profSelect.innerHTML = '<option value="">Sélectionner un professeur</option>';
            data.professeurs.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id; opt.textContent = p.nom_complet;
                if (p.id == selectedProfId) opt.selected = true;
                profSelect.appendChild(opt);
            });
        } catch (error) {
            console.error('Error fetching filtered data:', error);
        }
    }

    groupeSelect.addEventListener('change', function() {
        refreshFilteredData(this.value);
    });

    moduleSelect.addEventListener('change', function() {
        refreshFilteredData(groupeSelect.value, this.value, profSelect.value, false);
    });

    const modalForm = document.getElementById('addSeanceForm');
    const modalMethod = document.getElementById('modal_method');
    const modalTitle = document.getElementById('modal_title_text');
    const storeUrl = "{{ route('admin.emplois.store') }}";

    document.querySelectorAll('.open-modal-add-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            modalForm.action = storeUrl;
            modalMethod.disabled = true;
            modalMethod.value = 'POST';
            modalTitle.textContent = 'Ajouter une nouvelle séance';

            const gId = this.getAttribute('data-groupe-id');
            const jour = this.getAttribute('data-jour');
            const hDebut = this.getAttribute('data-heure-debut');
            const hFin = this.getAttribute('data-heure-fin');

            groupeSelect.value = gId;
            refreshFilteredData(gId);
            
            let targetDate = new Date();
            const daysMap = {'Dimanche':0, 'Lundi':1, 'Mardi':2, 'Mercredi':3, 'Jeudi':4, 'Vendredi':5, 'Samedi':6};
            const targetDay = daysMap[jour] !== undefined ? daysMap[jour] : targetDate.getDay();
            
            if (targetDate.getDay() !== targetDay) {
                targetDate.setDate(targetDate.getDate() + ((targetDay + 7 - targetDate.getDay()) % 7));
            }
            
            dateDebutInput.value = targetDate.toISOString().split('T')[0];
            // document.getElementById('modal_date_fin_validite').value = '';
            document.getElementById('modal_actif').checked = true;
            typeSelect.value = 'Présentiel';
            toggleSalle();

            forcedDebut = hDebut;
            forcedFin = hFin;
            updateSlots();
            
            new bootstrap.Modal(document.getElementById('addSeanceModal')).show();
        });
    });

    document.querySelectorAll('.open-modal-edit-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-seance-id');
            modalForm.action = `{{ url('admin/emplois') }}/${id}`;
            modalMethod.disabled = false;
            modalMethod.value = 'PUT';
            modalTitle.textContent = 'Modifier la séance';
            
            const gId = this.getAttribute('data-groupe-id');
            const mId = this.getAttribute('data-module-id');
            const pId = this.getAttribute('data-professeur-id');
            const type = this.getAttribute('data-type-seance');
            const salleId = this.getAttribute('data-salle-id');
            const dateDebut = this.getAttribute('data-date-debut');
            const dateFin = this.getAttribute('data-date-fin');
            const hDebut = this.getAttribute('data-heure-debut');
            const hFin = this.getAttribute('data-heure-fin');
            const actif = this.getAttribute('data-actif');

            groupeSelect.value = gId;
            refreshFilteredData(gId, mId, pId);
            
            typeSelect.value = type;
            toggleSalle();
            if (salleId) salleSelect.value = salleId;

            if (dateDebut) {
                dateDebutInput.value = dateDebut.split(' ')[0]; // Handle timestamp
            }
            
            if (dateFin) {
                // document.getElementById('modal_date_fin_validite').value = dateFin.split(' ')[0];
            } else {
                // document.getElementById('modal_date_fin_validite').value = '';
            }
            
            document.getElementById('modal_actif').checked = (actif === '1');

            forcedDebut = hDebut;
            forcedFin = hFin;
            updateSlots();
            
            new bootstrap.Modal(document.getElementById('addSeanceModal')).show();
        });
    });
});
</script>

<style>
    .matrix-container {
        max-height: 80vh;
        border: 1px solid #dee2e6;
    }

    .matrix-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* Important for performance and alignment */
    }

    .matrix-table th, .matrix-table td {
        border: 1px solid #dee2e6;
        padding: 0;
        text-align: center;
        font-size: 0.75rem;
    }

    /* Column Widths */
    .group-header, .group-cell {
        width: 45px;
        min-width: 45px;
        background-color: #f8f9fa;
        position: sticky;
        left: 0;
        z-index: 10;
        border-right: 2px solid #333 !important;
    }

    .slot-header, .matrix-cell {
        width: 65px;
        min-width: 65px;
    }

    /* Headers Styling */
    .day-header {
        background-color: #343a40;
        color: white;
        padding: 5px 0;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .slot-header {
        background-color: #e9ecef;
        padding: 3px 0;
        font-weight: 800;
        color: #495057;
    }

    /* Group Cells - Horizontal Rotation */
    .group-cell {
        background-color: #f8f9fa;
    }

    .rotated-text {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        white-space: nowrap;
        padding: 10px 0;
        font-weight: bold;
        color: #333;
        width: 100%;
        text-align: center;
        height: 120px; /* Base height for rows */
    }

    /* Cell Content */
    .matrix-cell {
        height: 120px;
        vertical-align: middle;
        position: relative;
    }

    .empty-cell {
        background-color: #2c3e50; /* Dark like the image */
    }

    .seance-content {
        padding: 5px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .module-code {
        font-weight: 800;
        font-size: 0.7rem;
        color: #000;
        margin-bottom: 3px;
    }

    .prof-name {
        font-size: 0.65rem;
        color: #555;
        margin-bottom: 2px;
    }

    .salle-name {
        font-weight: bold;
        font-size: 0.6rem;
        color: #0d6efd;
    }

    .teams-cell {
        background-color: #e3f2fd;
    }

    .teams-cell .salle-name {
        color: #0dcaf0;
    }

    .examen-cell {
        background-color: #fff5f5;
        border-left: 3px solid #dc3545 !important;
    }

    .cell-actions {
        position: absolute;
        bottom: 2px;
        right: 2px;
        opacity: 0;
        transition: opacity 0.2s;
        z-index: 10;
    }

    .matrix-cell:hover .cell-actions {
        opacity: 1;
    }

    .empty-cell-actions {
        opacity: 0;
        transition: opacity 0.2s;
    }

    .matrix-cell:hover .empty-cell-actions {
        opacity: 0.8;
    }

    .hover-add-btn {
        background-color: rgba(255,255,255,0.2) !important;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .hover-add-btn:hover {
        background-color: rgba(255,255,255,0.4) !important;
        transform: scale(1.1);
    }

    /* Color strips for days to help navigation */
    .header-lundi { border-top: 4px solid #007bff; }
    .header-mardi { border-top: 4px solid #6610f2; }
    .header-mercredi { border-top: 4px solid #6f42c1; }
    .header-jeudi { border-top: 4px solid #e83e8c; }
    .header-vendredi { border-top: 4px solid #fd7e14; }
    .header-samedi { border-top: 4px solid #28a745; }

    /* Sticky headers */
    thead th {
        position: sticky;
        top: 0;
        z-index: 5;
    }
    thead tr:nth-child(2) th {
        top: 32px; /* Adjust based on first header row height */
    }
</style>
@endsection


