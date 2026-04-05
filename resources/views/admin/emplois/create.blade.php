?@extends('layouts.admin')

@section('title', 'Nouvelle Séance')
@section('subtitle', 'Ajouter une nouvelle séance à l\'emploi du temps')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Informations de la séance</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.emplois.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-check form-switch p-3 bg-light rounded border">
                                <input class="form-check-input ms-0" type="checkbox" id="is_ramadan" name="is_ramadan" value="1">
                                <label class="form-check-label ms-2 fw-bold" for="is_ramadan">
                                    <i class="bi bi-moon-stars-fill text-primary me-1"></i> Mode Ramadan
                                </label>
                                <small class="text-muted ms-3">Active les horaires spécifiques au mois de Ramadan</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 d-none">
                            <label for="jour" class="form-label">Jour <span class="text-danger">*</span></label>
                            <input type="hidden" name="jour" id="jour" value="{{ old('jour') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="date_debut_validite" class="form-label">Date début <span class="text-danger">*</span></label>
                            @php
                                $defaultDate = now();
                                if (request('jour')) {
                                    $daysMap = ['Dimanche'=>0, 'Lundi'=>1, 'Mardi'=>2, 'Mercredi'=>3, 'Jeudi'=>4, 'Vendredi'=>5, 'Samedi'=>6];
                                    $targetDay = $daysMap[request('jour')] ?? $defaultDate->dayOfWeek;
                                    if ($defaultDate->dayOfWeek != $targetDay) {
                                        $defaultDate = $defaultDate->next($targetDay);
                                    }
                                }
                            @endphp
                            <input type="date" class="form-control @error('date_debut_validite') is-invalid @enderror" id="date_debut_validite" name="date_debut_validite" value="{{ old('date_debut_validite', $defaultDate->format('Y-m-d')) }}" required>
                            @error('date_debut_validite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="heure_debut" class="form-label">Heure début <span class="text-danger">*</span></label>
                            <select class="form-select @error('heure_debut') is-invalid @enderror" id="heure_debut" name="heure_debut" required>
                                <option value="">Choisir la date d'abord</option>
                            </select>
                            @error('heure_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="heure_fin" class="form-label">Heure fin <span class="text-danger">*</span></label>
                            <select class="form-select @error('heure_fin') is-invalid @enderror" id="heure_fin" name="heure_fin" required>
                                <option value="">Choisir début</option>
                            </select>
                            @error('heure_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="groupe_id" class="form-label">Groupe <span class="text-danger">*</span></label>
                            <select class="form-select @error('groupe_id') is-invalid @enderror" id="groupe_id" name="groupe_id" required>
                                <option value="">Sélectionner un groupe</option>
                                @foreach($groupes as $groupe)
                                    <option value="{{ $groupe->id }}" {{ old('groupe_id', request('groupe_id')) == $groupe->id ? 'selected' : '' }}>
                                        {{ $groupe->nom }} - {{ $groupe->filiere->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('groupe_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="module_id" class="form-label">Module <span class="text-danger">*</span></label>
                            <select class="form-select @error('module_id') is-invalid @enderror" id="module_id" name="module_id" required>
                                <option value="">Sélectionner un module</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module->id }}" {{ old('module_id') == $module->id ? 'selected' : '' }}>
                                        {{ $module->code }} - {{ $module->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('module_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="professeur_id" class="form-label">Professeur <span class="text-danger">*</span></label>
                            <select class="form-select @error('professeur_id') is-invalid @enderror" id="professeur_id" name="professeur_id" required>
                                <option value="">Sélectionner un professeur</option>
                                @foreach($professeurs as $prof)
                                    <option value="{{ $prof->id }}" {{ old('professeur_id') == $prof->id ? 'selected' : '' }}>
                                        {{ $prof->nom_complet }}
                                    </option>
                                @endforeach
                            </select>
                            @error('professeur_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="type_seance" class="form-label">Type de séance <span class="text-danger">*</span></label>
                            <select class="form-select @error('type_seance') is-invalid @enderror" id="type_seance" name="type_seance" required>
                                <option value="Présentiel" {{ old('type_seance') == 'Présentiel' ? 'selected' : '' }}>Présentiel</option>
                                <option value="Teams" {{ old('type_seance') == 'Teams' ? 'selected' : '' }}>Teams</option>
                            </select>
                            @error('type_seance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4" id="salle_container">
                            <label for="salle_id" class="form-label">Salle <span class="text-danger">*</span></label>
                            <select class="form-select @error('salle_id') is-invalid @enderror" id="salle_id" name="salle_id">
                                <option value="">Choisir</option>
                                @foreach($salles as $salle)
                                    <option value="{{ $salle->id }}" {{ old('salle_id') == $salle->id ? 'selected' : '' }}>
                                        {{ $salle->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('salle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const typeSelect = document.getElementById('type_seance');
                            const salleContainer = document.getElementById('salle_container');
                            const salleSelect = document.getElementById('salle_id');

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

                            // Slot switching logic
                            const slots = @json($slots);
                            const dateDebutInput = document.getElementById('date_debut_validite');
                            const jourInput = document.getElementById('jour');
                            const ramadanCheck = document.getElementById('is_ramadan');
                            const debutSelect = document.getElementById('heure_debut');
                            const finSelect = document.getElementById('heure_fin');
                            let isFirstLoad = true;

                            function getFrenchDay(dateString) {
                                if (!dateString) return '';
                                const days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                                const date = new Date(dateString);
                                return days[date.getDay()];
                            }

                            function updateSlots() {
                                const jour = getFrenchDay(dateDebutInput.value);
                                if (jourInput) jourInput.value = jour;
                                const isRamadan = ramadanCheck.checked;
                                
                                let currentSlots = [];
                                if (isRamadan) {
                                    currentSlots = (jour === 'Vendredi') ? slots.ramadan_friday : slots.ramadan_standard;
                                } else {
                                    currentSlots = (jour === 'Vendredi') ? slots.friday : slots.standard;
                                }

                                const oldDebut = isFirstLoad ? "{{ old('heure_debut', request('heure_debut')) }}" : debutSelect.value;
                                const oldFin = isFirstLoad ? "{{ old('heure_fin', request('heure_fin')) }}" : finSelect.value;

                                debutSelect.innerHTML = '<option value="">Début</option>';
                                finSelect.innerHTML = '<option value="">Fin</option>';

                                for (const [debut, fin] of Object.entries(currentSlots)) {
                                    const dOpt = document.createElement('option');
                                    dOpt.value = debut;
                                    dOpt.textContent = debut;
                                    if (debut === oldDebut) dOpt.selected = true;
                                    debutSelect.appendChild(dOpt);

                                    const fOpt = document.createElement('option');
                                    fOpt.value = fin;
                                    fOpt.textContent = fin;
                                    if (fin === oldFin) fOpt.selected = true;
                                    finSelect.appendChild(fOpt);
                                }
                                isFirstLoad = false;
                            }

                            dateDebutInput.addEventListener('change', updateSlots);
                            ramadanCheck.addEventListener('change', updateSlots);
                            updateSlots();

                            // Dynamic Filtering based on Groupe
                            const groupeSelect = document.getElementById('groupe_id');
                            const moduleSelect = document.getElementById('module_id');
                            const profSelect = document.getElementById('professeur_id');

                            const INITIAL_MODULE = "{{ old('module_id', $emploi->module_id ?? '') }}";
                            const INITIAL_PROF = "{{ old('professeur_id', $emploi->professeur_id ?? '') }}";

                            async function refreshFilteredData(groupeId, moduleId = null, updateModules = true) {
                                if (!groupeId) return;

                                try {
                                    let url = `{{ route('admin.emplois.filter-data') }}?groupe_id=${groupeId}`;
                                    if (moduleId) url += `&module_id=${moduleId}`;

                                    const response = await fetch(url);
                                    const data = await response.json();

                                    // Update Modules
                                    if (updateModules) {
                                        const currentModule = moduleSelect.value || INITIAL_MODULE;
                                        moduleSelect.innerHTML = '<option value="">Sélectionner un module</option>';
                                        data.modules.forEach(m => {
                                            const opt = document.createElement('option');
                                            opt.value = m.id;
                                            opt.textContent = `${m.code} - ${m.nom}`;
                                            if (m.id == currentModule) opt.selected = true;
                                            moduleSelect.appendChild(opt);
                                        });
                                    }

                                    // Update Professeurs
                                    const currentProf = profSelect.value || INITIAL_PROF;
                                    profSelect.innerHTML = '<option value="">Sélectionner un professeur</option>';
                                    data.professeurs.forEach(p => {
                                        const opt = document.createElement('option');
                                        opt.value = p.id;
                                        opt.textContent = p.nom_complet;
                                        if (p.id == currentProf) opt.selected = true;
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
                                refreshFilteredData(groupeSelect.value, this.value, false);
                            });

                            // Run once on load if group is pre-selected
                            if (groupeSelect.value) {
                                const initialModule = moduleSelect.value || INITIAL_MODULE;
                                refreshFilteredData(groupeSelect.value, initialModule, true);
                            }
                        });
                    </script>


                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1" {{ old('actif', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="actif">Séance active</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.emplois.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Annuler</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i> Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


