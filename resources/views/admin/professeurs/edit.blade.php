?@extends('layouts.admin')

@section('title', 'Modifier Professeur')
@section('subtitle', $professeur->nom_complet)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Modifier le professeur</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.professeurs.update', $professeur) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="matricule" class="form-label">Matricule <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('matricule') is-invalid @enderror"
                                   id="matricule" name="matricule" value="{{ old('matricule', $professeur->matricule) }}" required>
                            @error('matricule')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom" name="nom"
                                   value="{{ old('nom', $professeur->nom) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="prenom" name="prenom"
                                   value="{{ old('prenom', $professeur->prenom) }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="{{ old('email', $professeur->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control" id="telephone" name="telephone"
                                   value="{{ old('telephone', $professeur->telephone) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="specialite" class="form-label">Spécialité</label>
                        <input type="text" class="form-control" id="specialite" name="specialite"
                               value="{{ old('specialite', $professeur->specialite) }}">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="max_heures_mensuel" class="form-label">
                                <i class="bi bi-clock-history me-1"></i>Max heures / mois
                            </label>
                            <input type="number" class="form-control @error('max_heures_mensuel') is-invalid @enderror"
                                   id="max_heures_mensuel" name="max_heures_mensuel"
                                   value="{{ old('max_heures_mensuel', $professeur->max_heures_mensuel) }}"
                                   min="1" max="200"
                                   placeholder="Ex: 40 (vide = pas de limite)">
                            @error('max_heures_mensuel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Laisser vide pour aucune limite d'heures</small>
                        </div>
                    </div>

                    @php
                        $heuresActuelles = $professeur->getHeuresMensuellesActuelles();
                        $maxHeures = $professeur->max_heures_mensuel;
                        $pourcentage = $maxHeures ? round(($heuresActuelles / $maxHeures) * 100) : 0;
                    @endphp

                    @if($maxHeures)
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Heures utilisées ce mois ({{ \App\Models\EmploiDuTemps::formatHeures($heuresActuelles) }} / {{ \App\Models\EmploiDuTemps::formatHeures($maxHeures) }})</label>
                            <div class="progress" style="height: 15px;">
                                <div class="progress-bar {{ $pourcentage >= 90 ? 'bg-danger' : ($pourcentage >= 70 ? 'bg-warning' : 'bg-success') }}"
                                     role="progressbar" style="width: {{ min($pourcentage, 100) }}%">
                                    {{ $pourcentage }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <hr class="my-4">
                    <h6 class="mb-3 text-primary"><i class="bi bi-link-45deg me-2"></i>Assignations administratives</h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="modules" class="form-label">Modules enseignés</label>
                            <select name="modules[]" id="modules" class="form-select @error('modules') is-invalid @enderror" multiple>
                                @php
                                    $groupedModules = collect();
                                    foreach($modules as $module) {
                                        if($module->filieres->isEmpty()) {
                                            $groupedModules->push(['filiere' => 'Modules Généraux / Sans Filière', 'module' => $module]);
                                        } else {
                                            foreach($module->filieres as $filiere) {
                                                $groupedModules->push(['filiere' => $filiere->nom . ' (' . $filiere->code . ')', 'module' => $module]);
                                            }
                                        }
                                    }
                                    $allGroups = $groupedModules->groupBy('filiere');
                                @endphp

                                @foreach($allGroups as $filiereName => $items)
                                    <optgroup label="{{ $filiereName }}">
                                        @foreach($items as $item)
                                            <option value="{{ $item['module']->id }}" 
                                                {{ (collect(old('modules', $professeur->modules->pluck('id')))->contains($item['module']->id)) ? 'selected' : '' }}>
                                                [{{ $item['module']->code }}] {{ $item['module']->nom }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <small class="text-muted">Sélection multiple supportée</small>
                            @error('modules')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="groupes" class="form-label">Groupes assignés</label>
                            <select name="groupes[]" id="groupes" class="form-select @error('groupes') is-invalid @enderror" multiple>
                                @foreach($groupes as $groupe)
                                    <option value="{{ $groupe->id }}" 
                                        {{ (collect(old('groupes', $professeur->groupes->pluck('id')))->contains($groupe->id)) ? 'selected' : '' }}>
                                        {{ $groupe->nom }} ({{ $groupe->filiere->code ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('groupes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label for="filieres" class="form-label">Filières assignées</label>
                            <select name="filieres[]" id="filieres" class="form-select @error('filieres') is-invalid @enderror" multiple>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" 
                                        {{ (collect(old('filieres', $professeur->filieres->pluck('id')))->contains($filiere->id)) ? 'selected' : '' }}>
                                        {{ $filiere->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('filieres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1"
                                   {{ old('actif', $professeur->actif) ? 'checked' : '' }}>
                            <label class="form-check-label" for="actif">Professeur actif</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.professeurs.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
        display: flex;
        flex-wrap: wrap;
        padding: 0 5px;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        background-color: var(--ofppt-blue);
        color: white;
        border: none;
        border-radius: 4px;
        padding: 2px 8px;
        margin: 4px 2px;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 5px;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#modules, #groupes, #filieres').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Sélectionner...',
            closeOnSelect: false
        });
    });
</script>
@endpush


