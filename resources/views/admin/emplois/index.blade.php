@extends('layouts.admin')

@section('title', 'Gestion des Séances')
@section('subtitle', 'Liste de toutes les séances')

@section('actions')

    <form action="{{ route('admin.emplois.generate') }}" method="POST" class="d-inline me-2">
        @csrf
        <button type="submit" class="btn btn-dark" title="Générer les instances de séances pour cette semaine">
            <i class="bi bi-calendar-plus me-2"></i> Initialiser la semaine
        </button>
    </form>
    <a href="{{ route('admin.emplois.grille') }}" class="btn btn-info me-2">
        <i class="bi bi-grid-3x3 me-2"></i> Voir la grille
    </a>
    <a href="{{ route('admin.emplois.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Nouvelle séance
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="d-flex">
            <input type="hidden" name="view" value="{{ request('view') }}">
            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher par jour, groupe, module ou professeur..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Rechercher
            </button>
            @if(request('search'))
                <a href="{{ route('admin.emplois.index', ['view' => request('view')]) }}" class="btn btn-secondary ms-2">
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
                        <th>Jour</th>
                        <th>Horaire</th>
                        <th>Groupe</th>
                        <th>Module</th>
                        <th>Professeur</th>
                        <th>Salle</th>
                        <th>Statut</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emplois as $emploi)
                        <tr>
                            <td><span class="badge bg-primary">{{ $emploi->jour }}</span></td>
                            <td>{{ $emploi->heure_debut }} - {{ $emploi->heure_fin }}</td>
                            <td>{{ $emploi->groupe->nom }}</td>
                            <td>{{ $emploi->module->nom }}</td>
                            <td>{{ $emploi->professeur->nom_complet }}</td>
                            <td>
                                @if($emploi->type_seance === 'Teams')
                                    <span class="badge bg-info">Teams</span>
                                @else
                                    {{ $emploi->salle->nom }}
                                @endif
                            </td>
                            <td>
                                @if($emploi->actif)<span class="badge bg-success">Actif</span>
                                @else<span class="badge bg-secondary">Inactif</span>@endif
                            </td>
                            <td>
                                @if(request('view') === 'trashed')
                                    <span class="badge bg-danger">Supprimé le {{ $emploi->deleted_at->format('d/m/Y') }}</span>
                                @else
                                    <a href="{{ route('admin.emplois.show', $emploi) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('admin.emplois.edit', $emploi) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.emplois.destroy', $emploi) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer la suppression (elle sera archivée) ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Aucune séance trouvée</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($emplois->hasPages())<div class="card-footer">{{ $emplois->links() }}</div>@endif
</div>
@endsection


