<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professeur;
use App\Models\User;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Filiere;
use App\Models\EmploiDuTemps;
use App\Http\Requests\ProfesseurRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ProfesseurController extends Controller
{
    public function index()
    {
        $search = request('search');
        $modules = Module::with('filieres')->orderBy('nom')->get();

        $professeurs = Professeur::when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nom', 'like', '%' . $search . '%')
                        ->orWhere('prenom', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('telephone', 'like', '%' . $search . '%')
                        ->orWhere('specialite', 'like', '%' . $search . '%')
                        ->orWhereRaw('CONCAT(prenom, " ", nom) LIKE ?', ['%' . $search . '%'])
                        ->orWhereRaw('CONCAT(nom, " ", prenom) LIKE ?', ['%' . $search . '%']);
                });
            })
            ->orderBy('nom')
            ->orderBy('prenom')
            ->paginate(10);

        return view('admin.professeurs.index', compact('professeurs', 'modules'));
    }

    public function create()
    {
        $modules = Module::with('filieres')->orderBy('nom')->get();
        $groupes = Groupe::with('filiere')->orderBy('nom')->get();
        $filieres = Filiere::orderBy('nom')->get();
        return view('admin.professeurs.create', compact('modules', 'groupes', 'filieres'));
    }

    public function store(ProfesseurRequest $request)
    {
        $data = $request->validated();

        // Créer automatiquement un compte utilisateur pour chaque professeur
        if ($data['email']) {
            if (User::where('email', $data['email'])->exists()) {
                return redirect()->back()->withErrors(['email' => 'Cet email est déjà utilisé par un autre utilisateur.']);
            }
            $user = User::create([
                'name' => $data['prenom'] . ' ' . $data['nom'],
                'email' => $data['email'],
                'password' => Hash::make('OFPPT@EMPLOI'),
                'role' => 'professeur',
                'force_password_change' => true,
            ]);
            $data['user_id'] = $user->id;
        }

        $professeur = Professeur::create($data);

        // Synchroniser les relations
        if (isset($data['modules'])) $professeur->modules()->sync($data['modules']);
        if (isset($data['groupes'])) $professeur->groupes()->sync($data['groupes']);
        if (isset($data['filieres'])) $professeur->filieres()->sync($data['filieres']);

        return redirect()->route('admin.professeurs.index')
            ->with('success', 'Professeur et compte utilisateur créés avec succès');
    }

    public function show(Professeur $professeur)
    {
        $professeur->load(['emploisDuTemps.groupe', 'emploisDuTemps.module', 'emploisDuTemps.salle']);
        return view('admin.professeurs.show', compact('professeur'));
    }

    public function edit(Professeur $professeur)
    {
        $modules = Module::with('filieres')->orderBy('nom')->get();
        $groupes = Groupe::with('filiere')->orderBy('nom')->get();
        $filieres = Filiere::orderBy('nom')->get();
        return view('admin.professeurs.edit', compact('professeur', 'modules', 'groupes', 'filieres'));
    }

    public function update(ProfesseurRequest $request, Professeur $professeur)
    {
        $data = $request->validated();
        $professeur->update($data);

        // Synchroniser le statut actif du compte utilisateur
        if ($professeur->user_id) {
            $professeur->user->update(['actif' => $professeur->actif]);
        }

        // Synchroniser les relations
        $professeur->modules()->sync($data['modules'] ?? []);
        $professeur->groupes()->sync($data['groupes'] ?? []);
        $professeur->filieres()->sync($data['filieres'] ?? []);

        return redirect()->route('admin.professeurs.index')
            ->with('success', 'Professeur modifié avec succès');
    }

    public function destroy(Professeur $professeur)
    {
        // Supprimer le compte utilisateur associé
        if ($professeur->user_id) {
            User::find($professeur->user_id)?->delete();
        }

        $professeur->delete();

        return redirect()->route('admin.professeurs.index')
            ->with('success', 'Professeur supprimé avec succès');
    }
    public function paie()
    {
        $demandesAnnulation = \App\Models\EmploiDuTemps::with(['professeur', 'module', 'groupe'])
            ->where('statut_approbation', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $recentValidations = \App\Models\SeanceRealisation::with(['emploiDuTemps.professeur', 'emploiDuTemps.module', 'emploiDuTemps.groupe'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        $recentAbsences = \App\Models\Attendance::with(['etudiant', 'seanceRealisation.emploiDuTemps.professeur', 'seanceRealisation.emploiDuTemps.module'])
            ->whereIn('status', ['Absent', 'Justifié'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('admin.professeurs.paie', compact('demandesAnnulation', 'recentValidations', 'recentAbsences'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|extensions:xlsx,xls,csv,txt,html|max:10240',
        ], [
            'file.required' => 'Veuillez sélectionner un fichier.',
            'file.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();
            
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            } catch (\Exception $e) {
                try {
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Html');
                    $spreadsheet = $reader->load($path);
                } catch (\Exception $e2) {
                    throw new \Exception("Le format du fichier n'est pas reconnu. Essayez .xlsx.");
                }
            }

            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            if (count($rows) < 2) throw new \Exception("Le fichier est vide.");

            $headers = array_map(function($h) { return mb_strtolower(trim((string)$h)); }, $rows[0]);
            $created = 0; $updated = 0; $errors = [];
            
            $globalModules = $request->input('module_ids', []);

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty(array_filter($row))) continue;
                
                $data = [];
                foreach ($headers as $idx => $h) {
                    if ($h === '') continue;
                    $data[$h] = $row[$idx] ?? null;
                }

                $res = $this->processRowManual($data, $globalModules);
                if ($res === 'created') $created++;
                elseif ($res === 'updated') $updated++;
                else if ($res !== null) $errors[] = "Ligne " . ($i+1) . ": " . $res;
            }

            $message = "{$created} professeur(s) importé(s).";
            return redirect()->route('admin.professeurs.index')
                ->with('success', $message)
                ->with('error', count($errors) > 0 ? 'Problèmes : ' . implode(', ', array_slice($errors, 0, 3)) : null);

        } catch (\Exception $e) {
            return redirect()->route('admin.professeurs.index')
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    private function processRowManual($data, $globalModules = [])
    {
        $matricule = $this->getValManual($data, ['mle', 'matricule', 'id']);
        $nom = $this->getValManual($data, ['nom', 'last name', 'lastname', 'formateur']);
        $prenom = $this->getValManual($data, ['prenom', 'prénom', 'first name', 'firstname']);

        if (!$matricule || !$nom) return "Données manquantes (Matricule ou Nom)";
        if (!$prenom) $prenom = "";

        $email = $this->getValManual($data, ['email', 'mail']) ?: (mb_strtolower($prenom . '.' . $nom) . '@ofppt-emploi.ma');
        $tel = $this->getValManual($data, ['telephone', 'phone', 'tel']);
        $spec = $this->getValManual($data, ['specialite', 'spécialité', 'domaine']);

        $user = \App\Models\User::updateOrCreate(
            ['email' => $email],
            [
                'name' => trim($prenom . ' ' . $nom),
                'password' => \Illuminate\Support\Facades\Hash::make('OFPPT@EMPLOI'),
                'role' => 'professeur',
                'force_password_change' => true,
                'actif' => true
            ]
        );

        $professeur = \App\Models\Professeur::where('matricule', $matricule)->first();
        $res = $professeur ? 'updated' : 'created';

        $professeur = \App\Models\Professeur::updateOrCreate(
            ['matricule' => $matricule],
            [
                'nom' => mb_strtoupper($nom),
                'prenom' => mb_convert_case($prenom, MB_CASE_TITLE, "UTF-8"),
                'email' => $email,
                'telephone' => $tel,
                'specialite' => $spec,
                'user_id' => $user->id,
                'actif' => true
            ]
        );

        if (!empty($globalModules)) {
            $professeur->modules()->syncWithoutDetaching($globalModules);
        }

        return $res;
    }

    private function getValManual($data, $keys)
    {
        $normData = [];
        foreach ($data as $k => $v) { $normData[$this->normalizeKeyManual($k)] = $v; }
        foreach ($keys as $key) {
            $nk = $this->normalizeKeyManual($key);
            if (isset($normData[$nk]) && trim((string)$normData[$nk]) !== '') return trim((string)$normData[$nk]);
        }
        return null;
    }

    private function normalizeKeyManual($key)
    {
        $key = mb_strtolower(trim((string)$key));
        $key = preg_replace('/[áàâäãå]/u', 'a', $key);
        $key = preg_replace('/[éèêë]/u', 'e', $key);
        $key = preg_replace('/[íìîï]/u', 'i', $key);
        $key = preg_replace('/[óòôöõ]/u', 'o', $key);
        $key = preg_replace('/[úùûü]/u', 'u', $key);
        $key = preg_replace('/[^a-z0-9]/', '', $key);
        return $key;
    }
}
