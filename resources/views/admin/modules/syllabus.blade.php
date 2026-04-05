?@extends('layouts.admin')

@section('title', 'Gestion du Syllabus')
@section('subtitle', 'Module: ' . $module->nom)

@section('actions')
    <a href="{{ route('admin.modules.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i> Retour aux modules
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-ol me-2"></i>Syllabus du Module</h5>
                <span class="badge bg-info text-dark">Total: {{ $items->sum('poids_pourcentage') }}%</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Titre</th>
                                <th>Poids</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td>{{ $item->ordre }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $item->titre }}</div>
                                        @if($item->description)
                                            <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->poids_pourcentage }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">Aucun chapitre défini pour ce module.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


