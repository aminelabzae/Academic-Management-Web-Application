?@extends('layouts.admin')

@section('title', 'Nouveau Professeur')
@section('subtitle', 'Ajouter un nouveau professeur')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Informations du professeur</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.professeurs.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="matricule" class="form-label">Matricule <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('matricule') is-invalid @enderror"
                                   id="matricule" name="matricule" value="{{ old('matricule') }}" required>
                            @error('matricule')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                   id="nom" name="nom" value="{{ old('nom') }}" required>
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('prenom') is-invalid @enderror"
                                   id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                            @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control @error('telephone') is-invalid @enderror"
                                   id="telephone" name="telephone" value="{{ old('telephone') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="specialite" class="form-label">Spécialité</label>
                        <input type="text" class="form-control @error('specialite') is-invalid @enderror"
                               id="specialite" name="specialite" value="{{ old('specialite') }}"
                               placeholder="Ex: Développement Web, Réseaux...">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="max_heures_mensuel" class="form-label">
                                <i class="bi bi-clock-history me-1"></i>Max heures / mois
                            </label>
                            <input type="number" class="form-control @error('max_heures_mensuel') is-invalid @enderror"
                                   id="max_heures_mensuel" name="max_heures_mensuel"
                                   value="{{ old('max_heures_mensuel') }}"
                                   min="1" max="200"
                                   placeholder="Ex: 40 (vide = pas de limite)">
                            @error('max_heures_mensuel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="modules" class="form-label">Modules assignés</label>
                            <select name="modules[]" id="modules" class="form-select" multiple>
                                @php
                                    $groupedModules = collect();
                                    foreach($modules as $module) {
                                        $modFilieres = $module->filieres ?? collect();
                                        if($modFilieres->isEmpty()) {
                                            $groupedModules->push(['filiere' => 'Modules Généraux / Sans Filière', 'module' => $module]);
                                        } else {
                                            foreach($modFilieres as $filiere) {
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
                                                {{ (collect(old('modules'))->contains($item['module']->id)) ? 'selected' : '' }}>
                                                [{{ $item['module']->code }}] {{ $item['module']->nom }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <small class="text-muted">Sélection multiple supportée</small>
                        </div>
                        <div class="col-md-4">
                            <label for="groupes" class="form-label">Groupes assignés</label>
                            <select name="groupes[]" id="groupes" class="form-select" multiple>
                                @foreach($groupes as $groupe)
                                    <option value="{{ $groupe->id }}" {{ collect(old('groupes'))->contains($groupe->id) ? 'selected' : '' }}>
                                        {{ $groupe->nom }} ({{ $groupe->filiere->code ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="filieres" class="form-label">Filières assignées</label>
                            <select name="filieres[]" id="filieres" class="form-select" multiple>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" {{ collect(old('filieres'))->contains($filiere->id) ? 'selected' : '' }}>
                                        {{ $filiere->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1"
                                       {{ old('actif', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="actif">Professeur actif</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="creer_compte" name="creer_compte" value="1">
                                <label class="form-check-label" for="creer_compte">
                                    Créer un compte utilisateur <small class="text-muted">(mot de passe: password123)</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.professeurs.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i> Enregistrer
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


