<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Filiere;
use App\Models\Professeur;
use App\Models\Groupe;
use App\Models\EmploiDuTemps;
use App\Http\Requests\ModuleRequest;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index()
    {
        $search = request('search');

        $modules = Module::with('filieres')
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('code', 'like', '%' . $search . '%')
                        ->orWhere('nom', 'like', '%' . $search . '%')
                        ->orWhereHas('filieres', function ($f) use ($search) {
                            $f->where('nom', 'like', '%' . $search . '%')
                              ->orWhere('code', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('code')
            ->paginate(10);

        return view('admin.modules.index', compact('modules'));
    }

    public function create()
    {
        $filieres = Filiere::where('active', true)->orderBy('nom')->get();
        return view('admin.modules.create', compact('filieres'));
    }

    public function store(ModuleRequest $request)
    {
        $module = Module::create($request->validated());
        $module->filieres()->sync($request->filiere_ids);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Module créé avec succès');
    }

    public function show(Module $module)
    {
        $module->load('filieres.groupes');
        return view('admin.modules.show', compact('module'));
    }

    public function edit(Module $module)
    {
        $filieres = Filiere::where('active', true)->orderBy('nom')->get();
        return view('admin.modules.edit', compact('module', 'filieres'));
    }

    public function update(ModuleRequest $request, Module $module)
    {
        $module->update($request->validated());
        $module->filieres()->sync($request->filiere_ids);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Module modifié avec succès');
    }

    public function destroy(Module $module)
    {
        $module->delete();

        return redirect()->route('admin.modules.index')
            ->with('success', 'Module supprimé avec succès');
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
            
            $created = 0; $updated = 0; $skipped = 0; $merged = 0;
            $errors = [];
            $processedRows = 0;
            $seenCodes = [];
            $collisions = [];

            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            } catch (\Exception $e) {
                try {
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Html');
                    $spreadsheet = $reader->load($path);
                } catch (\Exception $e2) {
                    throw new \Exception("Le format du fichier n'est pas reconnu ($extension).");
                }
            }

            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            if (count($rows) < 1) throw new \Exception("Le fichier est vide.");

            // 1. Header discovery
            $bestHeaderIdx = 0;
            $maxScore = -1;
            $headerKeywords = ['module', 'code', 'nom', 'filiere', 'filiére', 'masse', 'horaire', 'coefficient', 'coeff', 'semestre', 'formateur', 'professeur'];
            
            foreach (array_slice($rows, 0, 15) as $idx => $row) {
                $rowStr = mb_strtolower(implode(' ', array_filter((array)$row)));
                $score = 0;
                foreach ($headerKeywords as $kw) {
                    if (str_contains($rowStr, $kw)) $score++;
                }
                if ($score > $maxScore) {
                    $maxScore = $score;
                    $bestHeaderIdx = $idx;
                }
            }

            $headers = (array)$rows[$bestHeaderIdx];
            
            for ($i = $bestHeaderIdx + 1; $i < count($rows); $i++) {
                $row = (array)$rows[$i];
                if (empty(array_filter($row))) continue;
                
                $processedRows++;
                $data = [];
                foreach ($headers as $hIdx => $h) {
                    $hStr = trim((string)$h);
                    if ($hStr === '') continue;
                    $data[$hStr] = $row[$hIdx] ?? null;
                }

                $res = $this->processRow($data);
                if ($res === null) {
                    $skipped++;
                    continue;
                }

                $code = $this->getVal($data, ['code module', 'code_module', 'code'], true);
                $nom = $this->getVal($data, ['nom module', 'nom_module', 'module', 'nom'], true);
                $idKey = mb_strtolower($code . '|' . $nom);
                
                if (isset($seenCodes[$idKey])) {
                    $merged++;
                } else {
                    $seenCodes[$idKey] = true;
                }

                if ($res === 'created') $created++;
                elseif ($res === 'updated') $updated++;
                else $errors[] = "Ligne " . ($i+1) . ": " . $res;
            }
        
            $unique = $created + $updated;
            $message = "Audit d'importation : Ligne d'en-tête détectée à l'index {$bestHeaderIdx}. ";
            $message .= "{$processedRows} lignes de données traitées. ";
            $message .= "Résultat : {$unique} module(s) unique(s) créés/mis à jour. ";
            if ($merged > 0) $message .= "{$merged} ligne(s) redondantes fusionnées. ";
            if ($skipped > 0) $message .= "{$skipped} ligne(s) vides ignorées. ";
            
            if (!empty($collisions)) {
                $uniqueCollisions = array_unique($collisions);
                $errors[] = "Attention : Collisions de noms détectées pour certains codes : " . implode(', ', array_slice($uniqueCollisions, 0, 3)) . (count($uniqueCollisions) > 3 ? "..." : "");
            }

            if (count($errors) > 0) {
                return redirect()->route('admin.modules.index')
                    ->with('success', $message)
                    ->with('error', 'Certaines lignes ont échoué : ' . implode(', ', array_slice($errors, 0, 3)) . (count($errors) > 3 ? '...' : ''));
            }

            return redirect()->route('admin.modules.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('admin.modules.index')
                ->with('error', 'Erreur lors de l\'importation : ' . $e->getMessage());
        }
    }

    private function processRow($data)
    {
        $code = $this->getVal($data, ['code module', 'code_module', 'code'], true);
        $nom = $this->getVal($data, ['nom module', 'nom_module', 'module', 'nom'], true);
        $masseH = $this->getVal($data, ['masse horaire', 'masse_horaire', 'heures', 'vol_horaire'], true) ?: 0;
        $coeff = $this->getVal($data, ['coefficient', 'coeff', 'coefficent'], true) ?: 1;
        $semestre = $this->getVal($data, ['semestre', 's'], true) ?: 1;
        $maxH = $this->getVal($data, ['max heures', 'max_heures', 'max_horaire'], true) ?: 0;
        $filiereRef = $this->getVal($data, ['code filiere', 'code_filiere', 'filiere', 'filieres', 'programme'], true);
        $profRef = $this->getVal($data, ['nom formateur', 'formateur affecte', 'formateur', 'professeur', 'enseignant'], true);
        $mleRef = $this->getVal($data, ['mle affecte', 'mle_affecte', 'mle formateur', 'mle', 'matricule'], true);
        $groupeRef = $this->getVal($data, ['groupe', 'nom groupe', 'code groupe'], true);

        if (!$code || !$nom) {
            // Check if it's just an empty row
            $vals = array_filter($data);
            if (empty($vals)) return null;

            $missing = [];
            if (!$code) $missing[] = "Code";
            if (!$nom) $missing[] = "Nom";
            
            // Helpful: list available keys to see why we missed Code/Nom
            $foundKeys = implode(', ', array_keys($data));
            return "Données manquantes (" . implode(', ', $missing) . "). Colonnes vues: [$foundKeys]";
        }

        $module = Module::where('code', $code)->where('nom', $nom)->first();
        $status = 'updated';
        $updateData = [
            'nom' => $nom,
            'masse_horaire' => (int)$masseH,
            'coefficient' => (float)$coeff,
            'semestre' => (int)$semestre,
            'max_heures_mensuel' => (int)$maxH,
        ];

        if (!$module) {
            $status = 'created';
            $module = Module::create(array_merge(['code' => $code, 'nom' => $nom], $updateData));
        } else {
            $module->update($updateData);
        }

        // Sync Filieres - Using syncWithoutDetaching to accumulate filieres across multiple rows
        if ($filiereRef) {
            $codes = array_map('trim', explode(',', $filiereRef));
            $filiereIds = Filiere::whereIn('code', $codes)->orWhereIn('nom', $codes)->pluck('id')->toArray();
            if (!empty($filiereIds)) {
                $module->filieres()->syncWithoutDetaching($filiereIds);
            }
        }

        // Sync Professor(s)
        $prof = null;
        if ($mleRef) {
            $prof = Professeur::where('matricule', trim($mleRef))->first();
        }
        
        if (!$prof && $profRef) {
            // Fuzzy match by name
            $cleanProfName = mb_strtolower(trim($profRef));
            if (!str_contains($cleanProfName, 'vacant') && !str_contains($cleanProfName, 'a affecter')) {
                $prof = Professeur::whereRaw('LOWER(CONCAT(prenom, " ", nom)) LIKE ?', ["%{$cleanProfName}%"])
                    ->orWhereRaw('LOWER(CONCAT(nom, " ", prenom)) LIKE ?', ["%{$cleanProfName}%"])
                    ->first();
            }
        }

        if ($prof) {
            $module->professeurs()->syncWithoutDetaching([$prof->id]);
            
            // Handle Group assignment if provided
            if ($groupeRef) {
                $this->handleAssociations($prof, $module, $groupeRef, $filiereRef);
            }
        }

        return $status;
    }

    private function handleAssociations($prof, $module, $groupeRef, $filiereRef)
    {
        // 1. Resolve Groupe
        $groupe = Groupe::where('nom', 'like', trim($groupeRef))->first();
        if (!$groupe && $filiereRef) {
            // Try to find group by name within a specific filiere
            $filiere = Filiere::where('code', $filiereRef)->orWhere('nom', 'like', $filiereRef)->first();
            if ($filiere) {
                $groupe = Groupe::where('filiere_id', $filiere->id)->where('nom', 'like', trim($groupeRef))->first();
            }
        }

        if ($groupe) {
            // 2. Create assignment in EmploiDuTemps if not exists
            $exists = EmploiDuTemps::where([
                'professeur_id' => $prof->id,
                'groupe_id' => $groupe->id,
                'module_id' => $module->id
            ])->exists();

            if (!$exists) {
                EmploiDuTemps::create([
                    'professeur_id' => $prof->id,
                    'groupe_id' => $groupe->id,
                    'module_id' => $module->id,
                    'jour' => 'Lundi', // Placeholder
                    'heure_debut' => '08:30:00',
                    'heure_fin' => '11:00:00',
                    'type_seance' => 'Présentiel',
                    'semaine_type' => 'Toutes',
                    'actif' => true,
                    'salle_id' => \App\Models\Salle::first()?->id
                ]);
            }
        }
    }

    private function getVal($data, $keys, $partialMatch = false)
    {
        $normData = [];
        foreach ($data as $k => $v) {
            $normData[$this->normalizeKey($k)] = $v;
        }

        // Tier 1: Prefer matches that are NOT just partial keywords
        foreach ($keys as $key) {
            $nk = $this->normalizeKey($key);
            if (isset($normData[$nk]) && trim((string)$normData[$nk]) !== '') {
                return trim((string)$normData[$nk]);
            }
        }

        if ($partialMatch) {
            foreach ($normData as $k => $v) {
                foreach ($keys as $target) {
                    $nt = $this->normalizeKey($target);
                    if ($nt === '') continue;

                    // Stricter partial matching:
                    // If looking for 'nom', avoid keys with 'code'
                    if (($nt === 'nom' || $nt === 'module') && str_contains($k, 'code')) continue;
                    // If looking for 'code', avoid keys that are just 'nom' or 'module'
                    if ($nt === 'code' && ($k === 'nom' || $k === 'module')) continue;

                    if (str_contains($k, $nt) && trim((string)$v) !== '') {
                        return trim((string)$v);
                    }
                }
            }
        }
        return null;
    }

    private function normalizeKey($key)
    {
        $key = mb_strtolower(trim($key));
        $key = preg_replace('/[áàâäãå]/u', 'a', $key);
        $key = preg_replace('/[éèêë]/u', 'e', $key);
        $key = preg_replace('/[íìîï]/u', 'i', $key);
        $key = preg_replace('/[óòôöõ]/u', 'o', $key);
        $key = preg_replace('/[úùûü]/u', 'u', $key);
        $key = preg_replace('/[^a-z0-9]/', '', $key);
        return $key;
    }
}
