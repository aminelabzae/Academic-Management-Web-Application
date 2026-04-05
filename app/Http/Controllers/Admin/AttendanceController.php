<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Etudiant;
use App\Models\Groupe;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['etudiant.groupe', 'seanceRealisation.emploiDuTemps.module', 'seanceRealisation.emploiDuTemps.professeur']);

        // Filtres
        if ($request->filled('etudiant_id')) {
            $query->where('etudiant_id', $request->etudiant_id);
        }

        if ($request->filled('groupe_id')) {
            $query->whereHas('etudiant', function($q) use ($request) {
                $q->where('groupe_id', $request->groupe_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_debut')) {
            $query->whereHas('seanceRealisation', function($q) use ($request) {
                $q->where('date', '>=', $request->date_debut);
            });
        }

        if ($request->filled('date_fin')) {
            $query->whereHas('seanceRealisation', function($q) use ($request) {
                $q->where('date', '<=', $request->date_fin);
            });
        }

        $attendances = $query->orderByDesc('created_at')->paginate(20);
        
        $groupes = Groupe::where('actif', true)->orderBy('nom')->get();
        $etudiants = Etudiant::orderBy('nom')->get();

        return view('admin.absences.index', compact('attendances', 'groupes', 'etudiants'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'status' => 'required|in:Présent,Absent,Justifié',
            'commentaire' => 'nullable|string|max:255'
        ]);

        $attendance->update($request->only(['status', 'commentaire']));

        return back()->with('success', 'Le statut de présence a été mis à jour.');
    }

    public function justify(Request $request, Attendance $attendance)
    {
        $attendance->update([
            'status' => 'Justifié',
            'commentaire' => 'Justifié par l\'administrateur le ' . now()->format('d/m/Y H:i')
        ]);

        return back()->with('success', 'L\'absence de ' . $attendance->etudiant->nom_complet . ' a été justifiée.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return back()->with('success', 'Enregistrement supprimé.');
    }
}
