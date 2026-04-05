@extends('layouts.admin')

@section('title', 'Gestion des Filières')
@section('subtitle', 'Liste de toutes les filières')

@section('actions')
    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-file-earmark-excel me-2"></i> Importer Excel
    </button>
    <a href="{{ route('admin.filieres.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Nouvelle filière
    </a>
@endsection

@section('content')

{{-- Import Error --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher par code, nom ou secteur..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Rechercher
            </button>
            @if(request('search'))
                <a href="{{ route('admin.filieres.index') }}" class="btn btn-secondary ms-2">
                    <i class="bi bi-x-circle"></i> Effacer
                </a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Nom</th>
                        <th>Niveau</th>
                        <th>Durée</th>
                        <th>Groupes</th>
                        <th>Modules</th>
                        <th>Statut</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filieres as $filiere)
                        <tr>
                            <td><strong>{{ $filiere->code }}</strong></td>
                            <td>{{ $filiere->nom }}</td>
                            <td>
                                <span class="badge bg-info">{{ $filiere->niveau }}</span>
                            </td>
                            <td>{{ $filiere->duree_formation }} ans</td>
                            <td>
                                <span class="badge bg-secondary">{{ $filiere->groupes_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $filiere->modules_count }}</span>
                            </td>
                            <td>
                                @if($filiere->active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.filieres.show', $filiere) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.filieres.edit', $filiere) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.filieres.destroy', $filiere->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Aucune filière trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($filieres->hasPages())
        <div class="card-footer px-4 py-3">
            {{ $filieres->links() }}
        </div>
    @endif
</div>

{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.filieres.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bi bi-file-earmark-excel me-2"></i>Importer des filières
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Fichier Excel (.xlsx, .xls) ou CSV</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text mt-2 text-info">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Vous pouvez désormais importer directement vos fichiers <strong>.xlsx</strong>.
                        </div>
                        @error('file')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Colonnes attendues dans le fichier :</strong>
                        <ul class="mb-0 mt-2">
                            <li>Année</li>
                            <li>Niveau</li>
                            <li>Secteur</li>
                            <li>Code Filière</li>
                            <li>Filière</li>
                            <li>Type de formation</li>
                        </ul>
                        <hr class="my-2">
                        <small class="text-muted">Les filières avec un code déjà existant seront ignorées (pas de doublons).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload me-2"></i>Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
