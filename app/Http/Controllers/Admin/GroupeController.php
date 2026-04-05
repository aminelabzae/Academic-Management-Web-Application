<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Groupe;
use App\Models\Filiere;
use App\Http\Requests\GroupeRequest;
use Illuminate\Http\Request;

class GroupeController extends Controller
{
    public function index()
    {
        $search = request('search');

        $groupes = Groupe::with('filiere')
            ->withCount('etudiants')
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nom', 'like', '%' . $search . '%')
                      ->orWhereHas('filiere', function($f) use ($search) {
                          $f->where('nom', 'like', '%' . $search . '%')
                            ->orWhere('code', 'like', '%' . $search . '%');
                      });
                });
            })
            ->orderBy('nom')
            ->paginate(10);

        return view('admin.groupes.index', compact('groupes'));
    }

    public function create()
    {
        $filieres = Filiere::where('active', true)->orderBy('nom')->get();
        return view('admin.groupes.create', compact('filieres'));
    }

    public function store(GroupeRequest $request)
    {
        Groupe::create($request->validated());

        return redirect()->route('admin.groupes.index')
            ->with('success', 'Groupe créé avec succès');
    }

    public function show(Groupe $groupe)
    {
        $groupe->load(['filiere', 'etudiants', 'emploisDuTemps.professeur', 'emploisDuTemps.module', 'emploisDuTemps.salle']);
        return view('admin.groupes.show', compact('groupe'));
    }

    public function edit(Groupe $groupe)
    {
        $filieres = Filiere::where('active', true)->orderBy('nom')->get();
        return view('admin.groupes.edit', compact('groupe', 'filieres'));
    }

    public function update(GroupeRequest $request, Groupe $groupe)
    {
        $groupe->update($request->validated());

        return redirect()->route('admin.groupes.index')
            ->with('success', 'Groupe modifié avec succès');
    }

    public function destroy(Groupe $groupe)
    {
        $groupe->delete();

        return redirect()->route('admin.groupes.index')
            ->with('success', 'Groupe supprimé avec succès');
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

            $message = "{$created} groupe(s) créé(s), {$updated} mis à jour.";
            return redirect()->route('admin.groupes.index')
                ->with('success', $message)
                ->with('error', count($errors) > 0 ? 'Problèmes : ' . implode(', ', array_slice($errors, 0, 3)) : null);

        } catch (\Exception $e) {
            return redirect()->route('admin.groupes.index')
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    private function processRowManual($data)
    {
        $nom = $this->getValManual($data, ['nom', 'groupe', 'nom groupe', 'nom_groupe']);
        $filiereCode = $this->getValManual($data, ['code filiere', 'filiere code', 'code_filiere', 'filiere', 'code filière', 'filière code', 'code_filière', 'code']);
        $annee = $this->getValManual($data, ['annee', 'année', 'niveau_annee', 'niveau_année', 'niveau']);
        $effectif = $this->getValManual($data, ['effectif', 'nombre stagiaires', 'nb']) ?: 0;
        $anneeScolaire = $this->getValManual($data, ['annee scolaire', 'année scolaire', 'annee_scolaire', 'année_scolaire', 'scolaire']) ?: date('Y') . '-' . (date('Y') + 1);

        if (!$nom || !$filiereCode || !$annee) {
            return "Données manquantes (Nom, Filière ou Année)";
        }

        $filiere = \App\Models\Filiere::where('code', $filiereCode)->first();
        if (!$filiere) return "Filière '$filiereCode' introuvable";

        $groupe = \App\Models\Groupe::where('nom', $nom)->where('annee_scolaire', $anneeScolaire)->first();
        $res = $groupe ? 'updated' : 'created';

        \App\Models\Groupe::updateOrCreate(
            ['nom' => $nom, 'annee_scolaire' => $anneeScolaire],
            [
                'filiere_id' => $filiere->id,
                'annee' => (int)$annee,
                'effectif' => (int)$effectif,
                'actif' => true
            ]
        );

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
