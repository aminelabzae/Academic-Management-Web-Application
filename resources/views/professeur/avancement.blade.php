@extends('layouts.professeur')

@section('title', 'Avancement des Modules')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-primary text-white">
            <div class="card-body py-4">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                        <i class="bi bi-graph-up-arrow fs-3"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">Avancement des Modules</h4>
                        <p class="mb-0 opacity-75">Suivez la progression pédagogique de vos cours</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @forelse($modules as $module)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-light text-primary border border-primary border-opacity-25 mb-2">{{ $module->code }}</span>
                            <h5 class="fw-bold text-dark mb-0">{{ $module->nom }}</h5>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold text-primary mb-0">{{ $module->progress_syllabus }}%</h3>
                        </div>
                    </div>
                    <div class="progress rounded-pill mb-3" style="height: 10px; background-color: #e9ecef;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                             role="progressbar" 
                             style="width: {{ $module->progress_syllabus }}%" 
                             aria-valuenow="{{ $module->progress_syllabus }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>
                
                <div class="card-body px-0 py-0 overflow-auto" style="max-height: 400px;">
                    <div class="list-group list-group-flush border-top border-bottom">
                        @forelse($module->syllabusItems as $item)
                            @php $isDone = $item->realisations->isNotEmpty(); @endphp
                            <div class="list-group-item border-0 py-3 px-4 {{ $isDone ? 'bg-success bg-opacity-10' : '' }}">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center overflow-hidden">
                                        @if($isDone)
                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 28px; height: 28px;">
                                                <i class="bi bi-check-lg fw-bold" style="font-size: 0.9rem;"></i>
                                            </div>
                                        @else
                                            <div class="bg-light text-muted border rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 28px; height: 28px;">
                                                <span class="small fw-bold">{{ $loop->iteration }}</span>
                                            </div>
                                        @endif
                                        <div class="text-truncate">
                                            <span class="d-block text-truncate {{ $isDone ? 'text-success fw-medium text-decoration-line-through' : 'text-dark' }}">
                                                {{ $item->titre }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ms-2 flex-shrink-0">
                                        <span class="badge {{ $isDone ? 'bg-success' : 'bg-light text-muted border' }} rounded-pill" style="font-size: 0.75rem;">
                                            {{ $item->poids_pourcentage }}%
                                        </span>
                                    </div>
                                </div>
                                @if($isDone && $item->realisations->first())
                                    <div class="ms-5 mt-1 small text-success opacity-75">
                                        <i class="bi bi-calendar-check me-1"></i> Terminé le {{ $item->realisations->first()->date->translatedFormat('d M Y') }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-journal-x fs-1 text-muted opacity-25 d-block mb-3"></i>
                                <p class="text-muted mb-0">Aucun chapitre défini pour ce module.</p>
                                <a href="{{ route('professeur.emploi') }}" class="btn btn-sm btn-outline-primary mt-3">
                                    <i class="bi bi-plus"></i> Ajouter un chapitre
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="card-footer bg-light border-0 py-3 px-4">
                    <div class="row g-0 align-items-center">
                        <div class="col-8">
                            <span class="text-muted small">
                                <i class="bi bi-info-circle me-1"></i> 
                                {{ $module->syllabusItems->filter(fn($i) => $i->realisations->isNotEmpty())->count() }} / {{ $module->syllabusItems->count() }} chapitres complétés
                            </span>
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{ route('professeur.emploi') }}" class="btn btn-sm btn-link p-0 text-decoration-none fw-bold" style="font-size: 0.8rem;">
                                Valider séance <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 py-5 text-center">
            <div class="card border-0 shadow-sm py-5 rounded-4">
                <div class="card-body">
                    <i class="bi bi-book fs-1 text-muted opacity-25 d-block mb-3"></i>
                    <h5 class="text-muted">Aucun module assigné n'a été trouvé.</h5>
                    <p class="text-muted small">Veuillez contacter l'administration pour vérifier vos modules.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection
