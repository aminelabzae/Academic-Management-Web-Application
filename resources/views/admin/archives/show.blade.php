@extends('layouts.admin')

@php
    $date = \Carbon\Carbon::create($year, $month, 1);
@endphp
@section('title', "Rapport Mensuel - " . ucfirst($date->translatedFormat('F Y')))
@section('subtitle', "Bilan des séances réalisées")

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.archives.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i>Retour aux archives mensuelles
    </a>
</div>

<div class="row">
    <!-- Statistiques par professeur -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Total par Professeur</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($statsProf as $stat)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $stat->professeur->nom_complet }}</span>
                            <span class="badge bg-primary rounded-pill">{{ round($stat->total_minutes / 60, 1) }}h</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Liste des séances réalisées -->
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Séances Réalisées</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Groupe</th>
                                <th>Module</th>
                                <th>Professeur</th>
                                <th>Durée</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($realisations as $r)
                                <tr>
                                    <td>{{ $r->date->format('d/m/Y') }}</td>
                                    <td>{{ $r->groupe->nom ?? 'N/A' }}</td>
                                    <td>{{ $r->module->nom ?? ($r->emploiDuTemps->module->nom ?? 'N/A') }}</td>
                                    <td>{{ $r->professeur->nom_complet ?? 'N/A' }}</td>
                                    <td>{{ round($r->duree_minutes / 60, 1) }}h</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
