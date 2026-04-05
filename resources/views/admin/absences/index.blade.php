@extends('layouts.admin')

@section('title', 'Gestion des Absences')
@section('subtitle', 'Suivi et justification des absences stagiaires')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.absences.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Groupe</label>
                <select name="groupe_id" class="form-select">
                    <option value="">Tous les groupes</option>
                    @foreach($groupes as $groupe)
                        <option value="{{ $groupe->id }}" {{ request('groupe_id') == $groupe->id ? 'selected' : '' }}>
                            {{ $groupe->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Stagiaire</label>
                <select name="etudiant_id" class="form-select">
                    <option value="">Tous les stagiaires</option>
                    @foreach($etudiants as $etudiant)
                        <option value="{{ $etudiant->id }}" {{ request('etudiant_id') == $etudiant->id ? 'selected' : '' }}>
                            {{ $etudiant->nom_complet }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Statut</label>
                <select name="status" class="form-select">
                    <option value="">Tous</option>
                    <option value="Absent" {{ request('status') == 'Absent' ? 'selected' : '' }}>Absent</option>
                    <option value="Justifié" {{ request('status') == 'Justifié' ? 'selected' : '' }}>Justifié</option>
                    <option value="Présent" {{ request('status') == 'Présent' ? 'selected' : '' }}>Présent</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Depuis le</label>
                <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-2"></i>Filtrer
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Stagiaire</th>
                        <th>Groupe</th>
                        <th>Module / Professeur</th>
                        <th>Statut</th>
                        <th>Commentaire</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>
                                {{ \Carbon\Carbon::parse($attendance->seanceRealisation->date)->format('d/m/Y') }}
                                <br><small class="text-muted">{{ $attendance->seanceRealisation->emploiDuTemps->heure_debut }}</small>
                            </td>
                            <td><strong>{{ $attendance->etudiant->nom_complet }}</strong></td>
                            <td>{{ $attendance->etudiant->groupe->nom }}</td>
                            <td>
                                {{ $attendance->seanceRealisation->emploiDuTemps->module->nom }}
                                <br><small class="text-muted">Par {{ $attendance->seanceRealisation->emploiDuTemps->professeur->nom_complet }}</small>
                            </td>
                            <td>
                                @if($attendance->status == 'Présent')
                                    <span class="badge bg-success">Présent</span>
                                @elseif($attendance->status == 'Absent')
                                    <span class="badge bg-danger">Absent</span>
                                @else
                                    <span class="badge bg-info">Justifié</span>
                                @endif
                            </td>
                            <td><small>{{ $attendance->commentaire ?: '-' }}</small></td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($attendance->status == 'Absent')
                                        <form action="{{ route('admin.absences.justify', $attendance) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Justifier l'absence">
                                                <i class="bi bi-shield-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $attendance->id }}" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>
                                
                                <!-- Modal Edit -->
                                <div class="modal fade" id="modalEdit{{ $attendance->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('admin.absences.update', $attendance) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Modifier le statut de présence</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Statut</label>
                                                        <select name="status" class="form-select">
                                                            <option value="Présent" {{ $attendance->status == 'Présent' ? 'selected' : '' }}>Présent</option>
                                                            <option value="Absent" {{ $attendance->status == 'Absent' ? 'selected' : '' }}>Absent</option>
                                                            <option value="Justifié" {{ $attendance->status == 'Justifié' ? 'selected' : '' }}>Justifié</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Commentaire / Justification</label>
                                                        <textarea name="commentaire" class="form-control" rows="3">{{ $attendance->commentaire }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Aucun enregistrement trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($attendances->hasPages())
        <div class="card-footer px-4 py-3">
            {{ $attendances->links() }}
        </div>
    @endif
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cibler tous les champs de commentaire dans les modals
    const textareas = document.querySelectorAll('textarea[name="commentaire"]');
    
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            // Trouver le menu déroulant "status" correspondant dans le même formulaire
            const form = this.closest('form');
            if (form) {
                const statusSelect = form.querySelector('select[name="status"]');
                // Si l'utilisateur commence à taper et que c'est sur "Absent", on passe à "Justifié"
                if (statusSelect && this.value.trim().length > 0 && statusSelect.value === 'Absent') {
                    statusSelect.value = 'Justifié';
                }
            }
        });
    });
});
</script>
@endpush
@endsection


