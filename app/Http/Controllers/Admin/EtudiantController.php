<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Etudiant;
use App\Models\Groupe;
use App\Models\User;
use App\Http\Requests\EtudiantRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EtudiantsImport;

class EtudiantController extends Controller
{
    public function index()
    {
        $search = request('search');

        $etudiants = Etudiant::with('groupe.filiere')
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('cef', 'like', '%' . $search . '%')
                        ->orWhere('nom', 'like', '%' . $search . '%')
                        ->orWhere('prenom', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhereRaw('CONCAT(prenom, " ", nom) LIKE ?', ['%' . $search . '%'])
                        ->orWhereRaw('CONCAT(nom, " ", prenom) LIKE ?', ['%' . $search . '%'])
                        ->orWhereHas('groupe', function ($g) use ($search) {
                            $g->where('nom', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.etudiants.index', compact('etudiants'));
    }

    public function create()
    {
        $groupes = Groupe::where('actif', true)->with('filiere')->orderBy('nom')->get();
        return view('admin.etudiants.create', compact('groupes'));
    }

    public function store(EtudiantRequest $request)
    {
        $data = $request->validated();

        // Créer automatiquement un compte utilisateur pour chaque étudiant
        if ($data['email']) {
            if (User::where('email', $data['email'])->exists()) {
                return redirect()->back()->withErrors(['email' => 'Cet email est déjà utilisé par un autre utilisateur.']);
            }
            $user = User::create([
                'name' => $data['prenom'] . ' ' . $data['nom'],
                'email' => $data['email'],
                'password' => Hash::make('OFPPT@EMPLOI'),
                'role' => 'etudiant',
                'force_password_change' => true,
            ]);
            $data['user_id'] = $user->id;
        }

        Etudiant::create($data);

        // Mettre à jour l'effectif du groupe
        $groupe = Groupe::find($data['groupe_id']);
        $groupe->update(['effectif' => $groupe->etudiants()->count()]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Étudiant et compte utilisateur créés avec succès');
    }

    public function show(Etudiant $etudiant)
    {
        $etudiant->load('groupe.filiere');
        return view('admin.etudiants.show', compact('etudiant'));
    }

    public function edit(Etudiant $etudiant)
    {
        $groupes = Groupe::where('actif', true)->with('filiere')->orderBy('nom')->get();
        return view('admin.etudiants.edit', compact('etudiant', 'groupes'));
    }

    public function update(EtudiantRequest $request, Etudiant $etudiant)
    {
        $oldGroupeId = $etudiant->groupe_id;
        $etudiant->update($request->validated());

        // Synchroniser le statut actif du compte utilisateur
        if ($etudiant->user_id) {
            $etudiant->user->update(['actif' => $etudiant->actif]);
        }

        // Mettre à jour les effectifs si le groupe a changé
        if ($oldGroupeId != $etudiant->groupe_id) {
            Groupe::find($oldGroupeId)?->update(['effectif' => Groupe::find($oldGroupeId)->etudiants()->count()]);
            Groupe::find($etudiant->groupe_id)?->update(['effectif' => Groupe::find($etudiant->groupe_id)->etudiants()->count()]);
        }

        return redirect()->route('admin.etudiants.index')
            ->with('success', 'Étudiant modifié avec succès');
    }

    public function destroy(Etudiant $etudiant)
    {
        $groupeId = $etudiant->groupe_id;

        if ($etudiant->user_id) {
            User::find($etudiant->user_id)?->delete();
        }

        $etudiant->delete();

        // Mettre à jour l'effectif
        Groupe::find($groupeId)?->update(['effectif' => Groupe::find($groupeId)->etudiants()->count()]);

        return redirect()->route('admin.etudiants.index')
            ->with('success', 'Étudiant supprimé avec succès');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|extensions:xlsx,xls,csv,txt,html|max:10240'
        ], [
            'file.required' => 'Veuillez sélectionner un fichier.',
            'file.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();
            
            // Smart loading using IOFactory (handles HTML tables, different XLS versions, etc.)
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            } catch (\Exception $e) {
                // Last ditch effort: Try to force HTML reader if it's a disguised HTML file
                try {
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Html');
                    $spreadsheet = $reader->load($path);
                } catch (\Exception $e2) {
                    throw new \Exception("Le format du fichier n'est pas reconnu. Essayez de l'enregistrer au format .xlsx standard.");
                }
            }

            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            if (count($rows) < 2) {
                throw new \Exception("Le fichier est vide ou ne contient pas d'en-têtes.");
            }

            // Find header row and data
            $headers = array_map(function($h) { return mb_strtolower(trim((string)$h)); }, $rows[0]);
            
            $created = 0; $updated = 0; $errors = [];
            
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty(array_filter($row))) continue;
                
                $data = [];
                foreach ($headers as $idx => $h) {
                    if ($h === '') continue;
                    $data[$h] = $row[$idx] ?? null;
                }

                $res = $this->processRowManual($data);
                if ($res === 'created') $created++;
                elseif ($res === 'updated') $updated++;
                else if ($res !== null) $errors[] = "Ligne " . ($i+1) . ": " . $res;
            }

            $message = "{$created} stagiaire(s) importé(s).";
            if (count($errors) > 0) {
                return redirect()->back()->with('success', $message)->with('error', 'Certains problèmes rencontrés : ' . implode(', ', array_slice($errors, 0, 3)));
            }
            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'importation : ' . $e->getMessage());
        }
    }

    private function processRowManual($data)
    {
        $cef = $this->getValManual($data, ['cef', 'id', 'matricule', 'identifiant']);
        $nom = $this->getValManual($data, ['nom', 'last name', 'lastname']);
        $prenom = $this->getValManual($data, ['prenom', 'prénom', 'first name', 'firstname']);
        $groupeName = $this->getValManual($data, ['groupe', 'classe', 'section', 'group']);
        $dateN = $this->getValManual($data, ['datenaissance', 'date naissance', 'naissance', 'dob']);

        if (!$cef || !$nom || !$prenom || !$groupeName) {
            return "Données manquantes (CEF, Nom, Prénom ou Groupe)";
        }

        $email = $cef . '@ofppt-emploi.ma';
        $groupe = \App\Models\Groupe::where('nom', 'like', $groupeName)->first();
        if (!$groupe) return "Groupe '$groupeName' introuvable";

        // Date handling
        $dateNaissance = null;
        if ($dateN) {
            try {
                if (is_numeric($dateN)) {
                    $dateNaissance = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateN);
                } else {
                    $dateNaissance = \Carbon\Carbon::parse($dateN);
                }
            } catch (\Exception $e) {}
        }

        $user = \App\Models\User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $prenom . ' ' . $nom,
                'password' => \Illuminate\Support\Facades\Hash::make('OFPPT@EMPLOI'),
                'role' => 'etudiant',
                'force_password_change' => true,
                'actif' => true,
            ]
        );

        $etudiant = \App\Models\Etudiant::where('cef', $cef)->first();
        $res = $etudiant ? 'updated' : 'created';

        \App\Models\Etudiant::updateOrCreate(
            ['cef' => $cef],
            [
                'nom' => mb_strtoupper($nom),
                'prenom' => mb_convert_case($prenom, MB_CASE_TITLE, "UTF-8"),
                'email' => $email,
                'date_naissance' => $dateNaissance,
                'groupe_id' => $groupe->id,
                'user_id' => $user->id,
                'actif' => true,
            ]
        );

        $groupe->update(['effectif' => $groupe->etudiants()->count()]);
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
