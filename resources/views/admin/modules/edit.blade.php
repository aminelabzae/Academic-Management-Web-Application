?@extends('layouts.admin')

@section('title', 'Modifier Module')
@section('subtitle', $module->nom)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Modifier le module</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.modules.update', $module) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   id="code" name="code" value="{{ old('code', $module->code) }}" required>
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-8">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom', $module->nom) }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="filiere_ids" class="form-label">Filières <span class="text-danger">*</span></label>
                            <select class="form-select @error('filiere_ids') is-invalid @enderror" id="filiere_ids" name="filiere_ids[]" multiple required style="height: 120px;">
                                @php $selectedFilieres = old('filiere_ids', $module->filieres->pluck('id')->toArray()); @endphp
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" {{ in_array($filiere->id, $selectedFilieres) ? 'selected' : '' }}>
                                        {{ $filiere->code }} - {{ $filiere->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Maintenez Ctrl pour sélectionner plusieurs filières</small>
                            @error('filiere_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="semestre" class="form-label">Semestre <span class="text-danger">*</span></label>
                            <select class="form-select" id="semestre" name="semestre" required>
                                @for($i = 1; $i <= 4; $i++)
                                    <option value="{{ $i }}" {{ old('semestre', $module->semestre) == $i ? 'selected' : '' }}>Semestre {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="coefficient" class="form-label">Coefficient</label>
                            <input type="number" class="form-control" id="coefficient" name="coefficient"
                                   value="{{ old('coefficient', $module->coefficient) }}" min="0" max="10" step="0.5">
                        </div>
                        <div class="col-md-6">
                            <label for="masse_horaire" class="form-label">Masse Horaire (Total)</label>
                            <input type="number" class="form-control @error('masse_horaire') is-invalid @enderror"
                                   id="masse_horaire" name="masse_horaire"
                                   value="{{ old('masse_horaire', $module->masse_horaire) }}" min="0">
                            @error('masse_horaire')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.modules.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Annuler</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i> Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


