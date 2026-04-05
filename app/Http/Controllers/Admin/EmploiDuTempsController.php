<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmploiDuTemps;
use App\Models\Professeur;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Salle;
use App\Services\ConflitService;
use App\Http\Requests\EmploiDuTempsRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class EmploiDuTempsController extends Controller
{
    protected $conflitService;

    public function __construct(ConflitService $conflitService)
    {
        $this->conflitService = $conflitService;
    }

    public function generateWeeklySessions(Request $request)
    {
        \Illuminate\Support\Facades\Artisan::call('app:generate-weekly-sessions', [
            'date' => $request->date ?? now()->startOfWeek()->toDateString()
        ]);
        
        return back()->with('success', 'Séances de la semaine générées avec succès.');
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = EmploiDuTemps::with(['professeur', 'groupe', 'module', 'salle']);

        if ($request->get('view') === 'trashed') {
            $query->onlyTrashed();
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('jour', 'like', '%' . $search . '%')
                  ->orWhereHas('groupe', function($g) use ($search) { $g->where('nom', 'like', '%' . $search . '%'); })
                  ->orWhereHas('module', function($m) use ($search) { 
                      $m->where('nom', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%'); 
                  })
                  ->orWhereHas('professeur', function($p) use ($search) {
                      $p->where('nom', 'like', '%' . $search . '%')
                        ->orWhere('prenom', 'like', '%' . $search . '%')
                        ->orWhereRaw('CONCAT(prenom, " ", nom) LIKE ?', ['%' . $search . '%'])
                        ->orWhereRaw('CONCAT(nom, " ", prenom) LIKE ?', ['%' . $search . '%']);
                  });
            });
        }

        $emplois = $query->orderBy('jour')
            ->orderBy('heure_debut')
            ->paginate(15)
            ->appends($request->all());

        return view('admin.emplois.index', compact('emplois'));
    }

    public function create()
    {
        $professeurs = Professeur::where('actif', true)->orderBy('nom')->orderBy('prenom')->get();
        $groupes = Groupe::where('actif', true)->with('filiere')->orderBy('nom')->get();
        $modules = Module::orderBy('nom')->get();
        $salles = Salle::where('disponible', true)->orderByRaw('LENGTH(numero)')->orderBy('numero')->get();
        $jours = EmploiDuTemps::JOURS;
        
        // Pass all possible variations to the view for JS handling
        $slots = [
            'standard' => EmploiDuTemps::getCreneaux('Lundi', false),
            'friday' => EmploiDuTemps::getCreneaux('Vendredi', false),
            'ramadan_standard' => EmploiDuTemps::getCreneaux('Lundi', true),
            'ramadan_friday' => EmploiDuTemps::getCreneaux('Vendredi', true),
        ];

        return view('admin.emplois.create', compact(
            'professeurs', 'groupes', 'modules', 'salles', 'jours', 'slots'
        ));
    }

    public function store(EmploiDuTempsRequest $request)
    {
        $data = $request->validated();

        if ($data['type_seance'] === 'Teams') {
            $data['salle_id'] = null;
        }

        // Déterminer le jour à partir de la date
        if (isset($data['date_debut_validite'])) {
            $date = \Carbon\Carbon::parse($data['date_debut_validite']);
            $daysMapping = [
                1 => 'Lundi',
                2 => 'Mardi',
                3 => 'Mercredi',
                4 => 'Jeudi',
                5 => 'Vendredi',
                6 => 'Samedi',
                0 => 'Dimanche',
            ];
            $data['jour'] = $daysMapping[$date->dayOfWeek];
        }

        // Vérifier les conflits
        $conflits = $this->conflitService->verifierConflits($data);

        if (!empty($conflits)) {
            $messages = $this->conflitService->formaterMessagesConflits($conflits);
            return back()->withErrors(['conflits' => $messages])->withInput();
        }

        // Vérification de la limite d'heures du professeur
        $professeur = Professeur::find($data['professeur_id']);
        if ($professeur && $professeur->max_heures_mensuel) {
            $debut = \Carbon\Carbon::parse($data['heure_debut']);
            $fin = \Carbon\Carbon::parse($data['heure_fin']);
            $dureeHeures = $debut->diffInMinutes($fin) / 60;
            
            $currentHeures = $professeur->getHeuresHebdomadairesActuelles();
            if (($currentHeures + $dureeHeures) > $professeur->max_heures_mensuel) {
                $maxFormatted = EmploiDuTemps::formatHeures($professeur->max_heures_mensuel);
                $currentFormatted = EmploiDuTemps::formatHeures($currentHeures);
                $dureeFormatted = EmploiDuTemps::formatHeures($dureeHeures);
                
                return back()->withErrors(['professeur_id' => "Ce professeur a atteint sa limite de {$maxFormatted}. (Charge actuelle: {$currentFormatted} + nouvelle séance: {$dureeFormatted} = " . EmploiDuTemps::formatHeures($currentHeures + $dureeHeures) . ")"])->withInput();
            }
        }

        // Vérification de la limite de la masse horaire du module
        $module = Module::find($data['module_id']);
        if ($module && $module->masse_horaire && isset($data['groupe_id'])) {
            $debut = \Carbon\Carbon::parse($data['heure_debut']);
            $fin = \Carbon\Carbon::parse($data['heure_fin']);
            $dureeHeures = $debut->diffInMinutes($fin) / 60;
            
            $heuresConsommees = $module->getHeuresTotalesByGroupe($data['groupe_id']);
            if (($heuresConsommees + $dureeHeures) > $module->masse_horaire) {
                $totalFormatted = EmploiDuTemps::formatHeures($module->masse_horaire);
                $consommeFormatted = EmploiDuTemps::formatHeures($heuresConsommees);
                $nouvelleFormatted = EmploiDuTemps::formatHeures($dureeHeures);
                
                return back()->withErrors(['module_id' => "Le module {$module->nom} a atteint sa masse horaire limite de {$totalFormatted} pour ce groupe. (Déjà planifié/réalisé: {$consommeFormatted} + cette séance: {$nouvelleFormatted} = " . EmploiDuTemps::formatHeures($heuresConsommees + $dureeHeures) . ")"])->withInput();
            }
        }

        EmploiDuTemps::create($data);

        if ($request->input('from_grille')) {
            return back()->with('success', 'Séance ajoutée avec succès');
        }

        return redirect()->route('admin.emplois.index')
            ->with('success', 'Séance ajoutée avec succès');
    }

    public function show(EmploiDuTemps $emploi)
    {
        $emploi->load(['professeur', 'groupe.filiere', 'module', 'salle']);
        return view('admin.emplois.show', compact('emploi'));
    }

    public function edit(EmploiDuTemps $emploi)
    {
        $professeurs = Professeur::where('actif', true)->orderBy('nom')->orderBy('prenom')->get();
        $groupes = Groupe::where('actif', true)->with('filiere')->orderBy('nom')->get();
        $modules = Module::orderBy('nom')->get();
        $salles = Salle::where('disponible', true)->orderByRaw('LENGTH(numero)')->orderBy('numero')->get();
        $jours = EmploiDuTemps::JOURS;
        
        $slots = [
            'standard' => EmploiDuTemps::getCreneaux('Lundi', false),
            'friday' => EmploiDuTemps::getCreneaux('Vendredi', false),
            'ramadan_standard' => EmploiDuTemps::getCreneaux('Lundi', true),
            'ramadan_friday' => EmploiDuTemps::getCreneaux('Vendredi', true),
        ];

        return view('admin.emplois.edit', compact(
            'emploi', 'professeurs', 'groupes', 'modules', 'salles', 'jours', 'slots'
        ));
    }

    public function update(EmploiDuTempsRequest $request, EmploiDuTemps $emploi)
    {
        $data = $request->validated();

        if ($data['type_seance'] === 'Teams') {
            $data['salle_id'] = null;
        }

        // Déterminer le jour à partir de la date
        if (isset($data['date_debut_validite'])) {
            $date = \Carbon\Carbon::parse($data['date_debut_validite']);
            $daysMapping = [
                1 => 'Lundi',
                2 => 'Mardi',
                3 => 'Mercredi',
                4 => 'Jeudi',
                5 => 'Vendredi',
                6 => 'Samedi',
                0 => 'Dimanche',
            ];
            $data['jour'] = $daysMapping[$date->dayOfWeek];
        }

        // Vérifier les conflits (en excluant la séance actuelle)
        $conflits = $this->conflitService->verifierConflits($data, $emploi->id);

        if (!empty($conflits)) {
            $messages = $this->conflitService->formaterMessagesConflits($conflits);
            return back()->withErrors(['conflits' => $messages])->withInput();
        }

        // Vérification de la limite d'heures du professeur (en excluant la séance en cours de modification)
        $professeur = Professeur::find($data['professeur_id'] ?? $emploi->professeur_id);
        if ($professeur && $professeur->max_heures_mensuel) {
            $debut = \Carbon\Carbon::parse($data['heure_debut'] ?? $emploi->heure_debut);
            $fin = \Carbon\Carbon::parse($data['heure_fin'] ?? $emploi->heure_fin);
            $dureeHeures = $debut->diffInMinutes($fin) / 60;
            
            $currentHeures = $professeur->getHeuresHebdomadairesActuelles($emploi->id);
            if (($currentHeures + $dureeHeures) > $professeur->max_heures_mensuel) {
                $maxFormatted = EmploiDuTemps::formatHeures($professeur->max_heures_mensuel);
                $currentFormatted = EmploiDuTemps::formatHeures($currentHeures);
                $dureeFormatted = EmploiDuTemps::formatHeures($dureeHeures);
                
                return back()->withErrors(['professeur_id' => "Ce professeur a atteint sa limite de {$maxFormatted}. (Charge actuelle: {$currentFormatted} + nouvelle séance: {$dureeFormatted} = " . EmploiDuTemps::formatHeures($currentHeures + $dureeHeures) . ")"])->withInput();
            }
        }

        // Vérification de la limite de la masse horaire du module
        $moduleId = $data['module_id'] ?? $emploi->module_id;
        $groupeId = $data['groupe_id'] ?? $emploi->groupe_id;
        $module = Module::find($moduleId);
        if ($module && $module->masse_horaire) {
            $debut = \Carbon\Carbon::parse($data['heure_debut'] ?? $emploi->heure_debut);
            $fin = \Carbon\Carbon::parse($data['heure_fin'] ?? $emploi->heure_fin);
            $dureeHeures = $debut->diffInMinutes($fin) / 60;
            
            // On exclut les séances liées à cet emploi car on teste la nouvelle durée
            $heuresConsommees = $module->getHeuresTotalesByGroupe($groupeId, $emploi->id);
            if (($heuresConsommees + $dureeHeures) > $module->masse_horaire) {
                $totalFormatted = EmploiDuTemps::formatHeures($module->masse_horaire);
                $consommeFormatted = EmploiDuTemps::formatHeures($heuresConsommees);
                $nouvelleFormatted = EmploiDuTemps::formatHeures($dureeHeures);
                
                return back()->withErrors(['module_id' => "Le module {$module->nom} a atteint sa masse horaire limite de {$totalFormatted} pour ce groupe. (Déjà planifié/réalisé: {$consommeFormatted} + cette séance: {$nouvelleFormatted} = " . EmploiDuTemps::formatHeures($heuresConsommees + $dureeHeures) . ")"])->withInput();
            }
        }

        $emploi->update($data);

        if ($request->input('from_grille')) {
            return back()->with('success', 'Séance modifiée avec succès');
        }

        return redirect()->route('admin.emplois.index')
            ->with('success', 'Séance modifiée avec succès');
    }

    public function destroy(EmploiDuTemps $emploi)
    {
        $emploi->delete();

        if (request()->has('from_grille') || str_contains(request()->header('referer', ''), 'emplois-grille-semaine')) {
             return redirect()->route('admin.emplois.grille-semaine', request()->query())->with('success', 'Séance supprimée avec succès');
        }

        return redirect()->route('admin.emplois.index')
            ->with('success', 'Séance supprimée avec succès');
    }

    public function approveAnnulation(EmploiDuTemps $emploi)
    {
        $emploi->update([
            'statut_approbation' => 'approved',
            'actif' => false,
        ]);

        // Notif Prof
        if ($emploi->professeur && $emploi->professeur->user) {
            $emploi->professeur->user->notify(new \App\Notifications\GenericNotification("Votre demande d'annulation pour la séance du {$emploi->jour} ({$emploi->heure_debut}) a été APPROUVÉE."));
        }

        $etudiants = \App\Models\Etudiant::where('groupe_id', $emploi->groupe_id)
            ->whereNotNull('user_id')
            ->get()
            ->unique('user_id');
            
        foreach ($etudiants as $etudiant) {
            if ($etudiant->user) {
                $etudiant->user->notify(new \App\Notifications\GenericNotification("⚠️ La séance de {$emploi->module->nom} du {$emploi->jour} ({$emploi->heure_debut}) a été ANNULÉE."));
            }
        }

        return back()->with('success', 'Annulation approuvée. Les étudiants ont été notifiés.');
    }

    public function rejectAnnulation(EmploiDuTemps $emploi)
    {
        $emploi->update([
            'statut_approbation' => 'rejected',
        ]);

        // Notif Prof
        if ($emploi->professeur && $emploi->professeur->user) {
            $emploi->professeur->user->notify(new \App\Notifications\GenericNotification("Votre demande d'annulation pour la séance du {$emploi->jour} ({$emploi->heure_debut}) a été REFUSÉE."));
        }

        return back()->with('warning', 'Annulation refusée. La séance reste active.');
    }

    public function grille(Request $request)
    {
        $groupes = Groupe::where('actif', true)->with('filiere')->orderBy('nom')->get();
        $groupeId = $request->get('groupe_id');
        $emplois = collect();
        $groupe = null;
        if ($groupeId) {
            $groupe = Groupe::with('filiere')->find($groupeId);
            $query = EmploiDuTemps::with(['professeur', 'module', 'salle'])
                ->where('groupe_id', $groupeId);
            
            if ($request->get('view') === 'trashed') {
                $query->onlyTrashed();
            } else {
                $query->where('actif', true);
            }
            
            $emplois = $query->get()->groupBy('jour');
        }
        $jours = EmploiDuTemps::JOURS;
        // For the search grid, we'll use a union of common slots or just standard ones as baseline
        $creneaux = EmploiDuTemps::getCreneaux('Lundi'); 
        return view('admin.emplois.grille', compact('groupes', 'groupe', 'emplois', 'jours', 'creneaux', 'groupeId'));
    }

    public function exportPdf(Request $request)
    {
        $groupeId = $request->get('groupe_id');
        if (!$groupeId) {
            return back()->with('error', 'Veuillez sélectionner un groupe');
        }
        $groupe = Groupe::with('filiere')->findOrFail($groupeId);
        $emplois = EmploiDuTemps::with(['professeur', 'module', 'salle'])
            ->where('groupe_id', $groupeId)
            ->where('actif', true)
            ->get()
            ->groupBy('jour');
        $jours = EmploiDuTemps::JOURS;
        $creneaux = EmploiDuTemps::getCreneaux('Lundi'); // Baseline for PDF
        $pdf = Pdf::loadView('admin.emplois.pdf', compact('groupe', 'emplois', 'jours', 'creneaux'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("emploi-du-temps-{$groupe->nom}.pdf");
    }

    public function grilleSemaine(Request $request)
    {
        $selectedAnnee = $request->get('annee');
        
        // Fetch groups based on year filter
        $groupesQuery = Groupe::where('actif', true)->with('filiere')->orderBy('nom');
        if ($selectedAnnee) {
            $groupesQuery->where('annee', $selectedAnnee);
        }
        $groupes = $groupesQuery->get();

        $query = EmploiDuTemps::with(['professeur', 'module', 'salle', 'groupe.filiere']);
        if ($request->get('view') === 'trashed') {
            $query->onlyTrashed();
        } else {
            $query->where('actif', true);
        }

        if ($selectedAnnee) {
            $query->whereHas('groupe', function($q) use ($selectedAnnee) {
                $q->where('annee', $selectedAnnee);
            });
        }

        $allSeances = $query->get();
        $jours = EmploiDuTemps::JOURS;
        $isRamadan = request()->has('ramadan') && request('ramadan') == 1;

        $emplois = [];
        foreach ($groupes as $groupe) {
            $emplois[$groupe->id] = [];
            foreach ($jours as $jour) {
                $emplois[$groupe->id][$jour] = [];
                $creneaux = array_keys(EmploiDuTemps::getCreneaux($jour, $isRamadan));
                
                foreach ($creneaux as $index => $heureDebut) {
                    // Trouver la séance qui commence exactement à cette heure pour ce groupe et ce jour
                    $seance = $allSeances->where('groupe_id', $groupe->id)
                        ->where('jour', $jour)
                        ->filter(function($s) use ($heureDebut) {
                            return substr($s->heure_debut, 0, 5) === substr($heureDebut, 0, 5);
                        })
                        ->first();
                    
                    $emplois[$groupe->id][$jour][$index] = $seance;
                }
            }
        }

        // Stats professeurs (heures hebdomadaires)
        $teacherStats = $this->calculateWeeklyTeacherStats();

        // Data for quick add modal
        $salles = Salle::where('disponible', true)->orderByRaw('LENGTH(numero)')->orderBy('numero')->get();
        $allSlots = [
            'standard' => EmploiDuTemps::getCreneaux('Lundi', false),
            'friday' => EmploiDuTemps::getCreneaux('Vendredi', false),
            'ramadan_standard' => EmploiDuTemps::getCreneaux('Lundi', true),
            'ramadan_friday' => EmploiDuTemps::getCreneaux('Vendredi', true),
        ];

        return view('admin.emplois.grille_semaine', compact('emplois', 'groupes', 'jours', 'selectedAnnee', 'teacherStats', 'isRamadan', 'salles', 'allSlots'));
    }

    public function exportGlobalPdf(Request $request)
    {
        $annee = $request->get('annee'); 
        $isRamadan = $request->has('ramadan');
        $weekRange = "Semaine du " . now()->startOfWeek()->format('d M') . " au " . now()->startOfWeek()->addDays(5)->format('d M Y');
        if ($isRamadan) $weekRange .= " (Mode Ramadan)";

        // Fetch groups
        $groupesQuery = Groupe::where('actif', true)->with('filiere')->orderBy('nom');
        $query = EmploiDuTemps::with(['professeur', 'module', 'salle', 'groupe.filiere'])
            ->where('actif', true);

        if ($annee) {
            $groupesQuery->where('annee', $annee);
            $query->whereHas('groupe', function($q) use ($annee) {
                $q->where('annee', $annee);
            });
            $title = ($annee == 1 ? "Premier" : ($annee == 2 ? "Deuxième" : "Troisième")) . " Année";
        } else {
            $title = "Toutes les Années";
        }
        
        $groupes = $groupesQuery->get();
        $allSeances = $query->get();
        $jours = EmploiDuTemps::JOURS;
        $isRamadan = $request->has('ramadan');

        $emplois = [];
        foreach ($groupes as $groupe) {
            $emplois[$groupe->id] = [];
            foreach ($jours as $jour) {
                $emplois[$groupe->id][$jour] = [];
                $creneaux = array_keys(EmploiDuTemps::getCreneaux($jour, $isRamadan));
                
                foreach ($creneaux as $index => $heureDebut) {
                    $seance = $allSeances->where('groupe_id', $groupe->id)
                        ->where('jour', $jour)
                        ->filter(function($s) use ($heureDebut) {
                            return substr($s->heure_debut, 0, 5) === substr($heureDebut, 0, 5);
                        })
                        ->first();
                    $emplois[$groupe->id][$jour][$index] = $seance;
                }
            }
        }

        $pdf = Pdf::loadView('admin.emplois.grille_global_pdf', compact('emplois', 'groupes', 'jours', 'title', 'weekRange', 'isRamadan'));
        $pdf->setPaper('A3', 'landscape');
        
        $filename = "emploi-global-" . ($annee ?: 'tous') . ($isRamadan ? '-ramadan' : '') . ".pdf";
        return $pdf->download($filename);
    }

    private function calculateWeeklyTeacherStats()
    {
        $emplois = EmploiDuTemps::with('professeur')
            ->where('actif', true)
            ->get();

        $stats = [];
        foreach ($emplois as $emploi) {
            if (!$emploi->professeur) continue;
            
            $profId = $emploi->professeur_id;
            $nom = $emploi->professeur->nom_complet;
            
            if (!isset($stats[$profId])) {
                $stats[$profId] = ['nom' => $nom, 'heures' => 0];
            }

            $debut = \Carbon\Carbon::parse($emploi->heure_debut);
            $fin = \Carbon\Carbon::parse($emploi->heure_fin);
            $heures = $debut->diffInMinutes($fin) / 60;
            
            $stats[$profId]['heures'] += $heures;
        }

        return collect($stats)->sortByDesc('heures');
    }

    public function getFilteredData(Request $request)
    {
        $groupeId = $request->get('groupe_id');
        $moduleId = $request->get('module_id');
        
        $response = [
            'modules' => [],
            'professeurs' => []
        ];

        // 1. If group is selected, get modules for this group's filiere
        if ($groupeId) {
            $groupe = Groupe::with('filiere')->find($groupeId);
            if ($groupe && $groupe->filiere) {
                $response['modules'] = $groupe->filiere->modules()
                    ->orderBy('nom')
                    ->get(['modules.id', 'modules.code', 'modules.nom']);
            }
        }

        // 2. Filter Professors
        $profQuery = Professeur::where('actif', true);

        if ($moduleId) {
            // Filter by specific module
            $profQuery->whereHas('modules', function($q) use ($moduleId) {
                $q->where('modules.id', $moduleId);
            });
        } elseif ($groupeId && !empty($response['modules'])) {
            // Fallback: Professors who can teach any module in this filiere
            $moduleIds = $response['modules']->pluck('id');
            $profQuery->whereHas('modules', function($q) use ($moduleIds) {
                $q->whereIn('modules.id', $moduleIds);
            });
        }

        $professeurs = $profQuery->orderBy('nom')->orderBy('prenom')->get(['id', 'nom', 'prenom']);

        $response['professeurs'] = $professeurs->map(function($p) {
            return [
                'id' => $p->id,
                'nom_complet' => $p->nom_complet
            ];
        });

        return response()->json($response);
    }
}
