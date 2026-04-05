@extends('layouts.admin')

@section('title', 'Archives Mensuelles')
@section('subtitle', 'Historique des mois passés')

@section('content')
<div class="row">
    @forelse($realisations as $v)
        @php
            $date = \Carbon\Carbon::create($v->year, $v->month, 1);
        @endphp
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="bi bi-calendar-range me-2"></i>{{ ucfirst($date->translatedFormat('F')) }}
                        </h5>
                        <span class="badge bg-secondary">{{ $v->year }}</span>
                    </div>
                    
                    <p class="text-muted small mb-3">
                        Mois complet ({{ $v->total_sessions }} séances réalisées)
                    </p>

                    <div class="row text-center bg-light rounded py-3 mb-3 mx-0">
                        <div class="col-6 border-end">
                            <h4 class="mb-0 text-dark">{{ $v->total_sessions }}</h4>
                            <small class="text-muted text-uppercase" style="font-size: 0.7rem;">Séances</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-0 text-dark">{{ round($v->total_minutes / 60, 1) }}h</h4>
                            <small class="text-muted text-uppercase" style="font-size: 0.7rem;">Total Heures</small>
                        </div>
                    </div>

                    <a href="{{ route('admin.archives.show', ['year' => $v->year, 'month' => $v->month]) }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-eye me-2"></i>Consulter le rapport
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="card py-5">
                <i class="bi bi-archive text-muted mb-3" style="font-size: 3rem;"></i>
                <h5>Aucune archive disponible</h5>
                <p class="text-muted">Les archives seront créées automatiquement au fur et à mesure que les professeurs valident leurs séances.</p>
            </div>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $realisations->links() }}
</div>
@endsection
