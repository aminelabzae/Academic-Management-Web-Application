?@extends('layouts.admin')

@section('title', $module->nom)
@section('subtitle', 'Détails du module')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Informations</h5></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th>Code:</th><td><strong>{{ $module->code }}</strong></td></tr>
                    <tr><th>Filières:</th><td>{{ $module->filieres->pluck('nom')->implode(', ') }}</td></tr>
                    <tr><th>Semestre:</th><td><span class="badge bg-info">S{{ $module->semestre }}</span></td></tr>
                    <tr><th>Coefficient:</th><td>{{ $module->coefficient }}</td></tr>
                    @php
                        $heuresHebdo = $module->getHeuresHebdomadairesActuelles();
                        $heuresTotales = $module->getHeuresTotalesByGroupe();
                        $maxHeures = $module->masse_horaire;
                    @endphp
                    <tr>
                        <th class="py-3"><i class="bi bi-clock me-1 text-primary"></i>Charge prévue:</th>
                        <td class="py-3">
                            <span class="badge bg-primary fs-6">{{ \App\Models\EmploiDuTemps::formatHeures($heuresHebdo) }} / mois</span>
                        </td>
                    </tr>
                    <tr class="border-bottom">
                        <th class="pb-3 text-muted"><i class="bi bi-calendar-check me-1"></i>Total consommé (Validé):</th>
                        <td class="pb-3 text-muted">{{ \App\Models\EmploiDuTemps::formatHeures($heuresTotales) }}</td>
                    </tr>

                    @if($maxHeures > 0)
                        <tr>
                            <th colspan="2" class="pt-4"><i class="bi bi-bar-chart-fill me-2"></i>Progression par groupe</th>
                        </tr>
                        @foreach($module->filieres as $filiere)
                            @foreach($filiere->groupes as $groupe)
                                @php
                                    $heuresGroupePrevues = $module->getHeuresHebdomadairesByGroupe($groupe->id);
                                    $heuresGroupeConsommees = $module->getHeuresTotalesByGroupe($groupe->id);
                                    // Le pourcentage est maintenant basé sur la charge prévue (automatique)
                                    $pourcentage = $maxHeures > 0 ? round(($heuresGroupePrevues / $maxHeures) * 100) : 0;
                                @endphp
                                <tr>
                                    <td colspan="2" class="pb-4">
                                        <div class="d-flex justify-content-between mb-1">
                                            <div>
                                                <small class="fw-bold">Groupe: {{ $groupe->nom }}</small>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-primary d-block fw-semibold">{{ \App\Models\EmploiDuTemps::formatHeures($heuresGroupePrevues) }} affectées / {{ $maxHeures }} h</small>
                                                <small class="text-success" style="font-size: 0.75rem;">Total validé: {{ \App\Models\EmploiDuTemps::formatHeures($heuresGroupeConsommees) }}</small>
                                            </div>
                                        </div>
                                        <div class="progress" style="height: 12px;">
                                            <div class="progress-bar {{ $pourcentage >= 100 ? 'bg-danger' : ($pourcentage >= 80 ? 'bg-warning' : 'bg-success') }}"
                                                 role="progressbar" style="width: {{ min($pourcentage, 100) }}%">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @else
                        <tr><th>Heures réalisées:</th><td>{{ \App\Models\EmploiDuTemps::formatHeures($module->getHeuresTotalesByGroupe(0)) }} (Global)</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('admin.modules.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Retour</a>
    <a href="{{ route('admin.modules.edit', $module) }}" class="btn btn-warning"><i class="bi bi-pencil me-2"></i> Modifier</a>
</div>
@endsection


