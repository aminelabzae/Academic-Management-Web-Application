<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use App\Models\EmploiDuTemps;
use App\Models\Professeur;
use App\Models\Attendance;
use Illuminate\Http\Request;

class EmploiController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $professeur = Professeur::where('user_id', $user->id)->first();

        $seancesAujourdhui = collect();
        $totalSeances = 0;

        if ($professeur) {
            $jourActuel = now()->locale('fr')->isoFormat('dddd');
            $jourActuel = ucfirst($jourActuel);

            $seancesAujourdhui = EmploiDuTemps::with(['groupe.etudiants', 'module', 'salle', 'realisations'])
                ->where('professeur_id', $professeur->id)
                ->where('jour', $jourActuel)
                ->where(function($q) {
                    $q->where('actif', true)
                      ->orWhere('statut_approbation', 'approved');
                })
                ->orderBy('heure_debut')
                ->get();

            $totalSeances = EmploiDuTemps::where('professeur_id', $professeur->id)
                ->where(function($q) {
                    $q->where('actif', true)
                      ->orWhere('statut_approbation', 'approved');
                })
                ->count();
                
            $hourStats = $professeur->getStatistiquesHeuresParModule();
        } else {
            $hourStats = [];
        }

        return view('professeur.dashboard', compact('professeur', 'seancesAujourdhui', 'totalSeances', 'hourStats'));
    }

    public function index()
    {
        $user = auth()->user();
        $professeur = Professeur::where('user_id', $user->id)->first();

        $emplois = collect();

        if ($professeur) {
            $emplois = EmploiDuTemps::with(['groupe.filiere', 'groupe.etudiants', 'module', 'salle', 'realisations'])
                ->where('professeur_id', $professeur->id)
                ->where(function($q) {
                    $q->where('actif', true)
                      ->orWhere('statut_approbation', 'approved');
                })
                ->get()
                ->groupBy('jour');
        }

        $jours = EmploiDuTemps::JOURS;
        
        $creneaux = EmploiDuTemps::getCreneaux('Lundi');

        return view('professeur.emploi', compact('professeur', 'emplois', 'jours', 'creneaux'));
    }

    public function demandeAnnulation(Request $request, EmploiDuTemps $emploi)
    {
        $user = auth()->user();
        $professeur = Professeur::where('user_id', $user->id)->first();
        if (!$professeur || $emploi->professeur_id !== $professeur->id) {
            return back()->with('error', 'Action non autorisée');
        }
        $request->validate(['reason' => 'required|string|max:500']);
        $emploi->update([
            'statut_approbation' => 'pending',
            'motif_annulation' => $request->reason,
        ]);

        // Notif Admins
        $admins = \App\Models\User::whereIn('role', ['admin', 'head_admin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\GenericNotification("⚠️ Une nouvelle demande d'annulation de séance de Mr/Mme {$professeur->nom} est en attente de validation."));
        }

        return back()->with('success', 'Votre demande d\'annulation a été envoyée.');
    }

    public function toggleExamen(EmploiDuTemps $emploi)
    {
        $user = auth()->user();
        $professeur = Professeur::where('user_id', $user->id)->first();
        if (!$professeur || $emploi->professeur_id !== $professeur->id) {
            return back()->with('error', 'Action non autorisée');
        }
        $emploi->update(['is_examen' => !$emploi->is_examen]);

        if ($emploi->is_examen) {
            $etudiants = \App\Models\Etudiant::where('groupe_id', $emploi->groupe_id)
                ->whereNotNull('user_id')
                ->get()
                ->unique('user_id');
                
            foreach ($etudiants as $etudiant) {
                if ($etudiant->user) {
                    $etudiant->user->notify(new \App\Notifications\GenericNotification("📅 Un EXAMEN a été programmé pour {$emploi->module->nom} le {$emploi->jour} ({$emploi->heure_debut})."));
                }
            }
        }

        return back()->with('success', 'Statut examen mis à jour.');
    }

    public function updateTeamsLink(Request $request, EmploiDuTemps $emploi)
    {
        $user = auth()->user();
        $professeur = Professeur::where('user_id', $user->id)->first();
        if (!$professeur || $emploi->professeur_id !== $professeur->id) {
            return back()->with('error', 'Action non autorisée');
        }

        $request->validate(['teams_link' => 'nullable|url']);
        
        $emploi->update(['teams_link' => $request->teams_link]);
        
        return back()->with('success', 'Lien de la séance mis à jour.');
    }

    public function confirmerSeance(Request $request, EmploiDuTemps $emploi)
    {
        $user = auth()->user();
        $professeur = Professeur::where('user_id', $user->id)->first();
        if (!$professeur || $emploi->professeur_id !== $professeur->id) {
            return back()->with('error', 'Action non autorisée');
        }

        // Vérifier si c'est bien aujourd'hui
        $jourActuel = ucfirst(now()->locale('fr')->isoFormat('dddd'));
        if ($emploi->jour !== $jourActuel) {
            return back()->with('error', 'Vous ne pouvez valider que les séances d\'aujourd\'hui.');
        }

        $moduleId = request('module_id');
        if ($moduleId && !\App\Models\Module::find($moduleId)) {
            return back()->with('error', 'Module invalide.');
        }

        // Calculer la durée actuelle
        $debut = \Carbon\Carbon::parse($emploi->heure_debut);
        $fin = \Carbon\Carbon::parse($emploi->heure_fin);
        $duree = $debut->diffInMinutes($fin);

        // Créer la réalisation
        $realisation = \App\Models\SeanceRealisation::firstOrCreate([
            'emploi_du_temps_id' => $emploi->id,
            'date' => now()->toDateString(),
        ], [
            'module_id' => $moduleId,
            'professeur_id' => $emploi->professeur_id,
            'groupe_id' => $emploi->groupe_id,
            'duree_minutes' => $duree
        ]);

        // Enregistrer les présences
        if ($request->has('attendance')) {
            foreach ($request->attendance as $etudiantId => $status) {
                Attendance::updateOrCreate([
                    'seance_realisation_id' => $realisation->id,
                    'etudiant_id' => $etudiantId
                ], [
                    'status' => $status,
                    'commentaire' => $request->commentaire[$etudiantId] ?? null
                ]);
            }
        }

        // Enregistrer le syllabus (chapitres traités)
        if ($request->has('syllabus_items')) {
            $realisation->syllabusItems()->sync($request->syllabus_items);
        }

        return back()->with('success', 'Séance validée, présences et syllabus enregistrés.');
    }

    public function seancesRealisees()
    {
        $user = auth()->user();
        $professeur = \App\Models\Professeur::where('user_id', $user->id)->first();
        
        if (!$professeur) {
            return back()->with('error', 'Action non autorisée');
        }

        $realisations = \App\Models\SeanceRealisation::whereHas('emploiDuTemps', function($q) use ($professeur) {
                $q->withTrashed()->where('professeur_id', $professeur->id);
            })
            ->with(['emploiDuTemps.module', 'emploiDuTemps.groupe', 'emploiDuTemps.salle'])
            ->orderBy('date', 'desc')
            ->get();

        return view('professeur.seances_realisees', compact('realisations', 'professeur'));
    }

    public function absences()
    {
        $user = auth()->user();
        $professeur = Professeur::where('user_id', $user->id)->first();

        if (!$professeur) return back();

        $absences = Attendance::whereHas('seanceRealisation', function($query) use ($professeur) {
                $query->whereHas('emploiDuTemps', function($q) use ($professeur) {
                    $q->withTrashed()->where('professeur_id', $professeur->id);
                });
            })
            ->with(['etudiant', 'seanceRealisation.emploiDuTemps.module', 'seanceRealisation.emploiDuTemps.groupe'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('professeur.absences', compact('absences'));
    }

    public function avancement()
    {
        $user = auth()->user();
        $professeur = Professeur::where('user_id', $user->id)->first();
        
        if (!$professeur) return back();

        $modules = $professeur->modules()->with(['syllabusItems.realisations'])->get();

        return view('professeur.avancement', compact('modules', 'professeur'));
    }
}
