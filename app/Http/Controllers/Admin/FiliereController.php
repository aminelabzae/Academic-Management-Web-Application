<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filiere;
use App\Http\Requests\FiliereRequest;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    public function index()
    {
        $search = request('search');

        $filieres = Filiere::withCount(['groupes', 'modules'])
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('code', 'like', '%' . $search . '%')
                      ->orWhere('nom', 'like', '%' . $search . '%')
                      ->orWhere('secteur', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('nom')
            ->paginate(10);

        return view('admin.filieres.index', compact('filieres'));
    }

    public function create()
    {
        $niveaux = Filiere::NIVEAUX;
        return view('admin.filieres.create', compact('niveaux'));
    }

    public function store(FiliereRequest $request)
    {
        Filiere::create($request->validated());

        return redirect()->route('admin.filieres.index')
            ->with('success', 'Filière créée avec succès');
    }

    public function show(Filiere $filiere)
    {
        $filiere->load(['groupes', 'modules']);
        return view('admin.filieres.show', compact('filiere'));
    }

    public function edit(Filiere $filiere)
    {
        $niveaux = Filiere::NIVEAUX;
        return view('admin.filieres.edit', compact('filiere', 'niveaux'));
    }

    public function update(FiliereRequest $request, Filiere $filiere)
    {
        $filiere->update($request->validated());

        return redirect()->route('admin.filieres.index')
            ->with('success', 'Filière modifiée avec succès');
    }

    public function destroy(Filiere $filiere)
    {
        try {
            $filiere->delete();

            return redirect()->route('admin.filieres.index')
                ->with('success', 'Filière supprimée avec succès');
        } catch (\Exception $e) {
            return redirect()->route('admin.filieres.index')
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
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
                    throw new \Exception("Le format du fichier n'est pas reconnu.");
                }
            }

            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            if (count($rows) < 2) throw new \Exception("Le fichier est vide.");
            
            $headers = array_map(function($h) { return mb_strtolower(trim((string)$h)); }, $rows[0]);
            $created = 0; $skipped = 0;

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty(array_filter($row))) continue;
                
                $data = [];
                foreach ($headers as $idx => $h) {
                    if ($h === '') continue;
                    $data[$h] = $row[$idx] ?? null;
                }

                if ($this->processRowManual($data)) $created++;
                else $skipped++;
            }

            $message = "{$created} filière(s) importée(s).";
            if ($skipped > 0) $message .= " {$skipped} ligne(s) ignorée(s).";

            return redirect()->route('admin.filieres.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('admin.filieres.index')->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    private function processRowManual($data)
    {
        $code = $this->getValManual($data, ['code', 'code filiere', 'code_filiere', 'code filière']);
        $nom = $this->getValManual($data, ['nom', 'filiere', 'filière', 'libelle']);
        $niveau = $this->getValManual($data, ['niveau', 'type']);
        $annee = $this->getValManual($data, ['annee', 'année', 'duree']);
        $secteur = $this->getValManual($data, ['secteur']);

        if (!$code) return false;

        if (\App\Models\Filiere::where('code', $code)->exists()) return false;

        \App\Models\Filiere::create([
            'code' => $code,
            'nom' => $nom ?? $code,
            'niveau' => $this->mapNiveauImport($niveau),
            'annee' => is_numeric($annee) ? (int)$annee : 2,
            'secteur' => $secteur,
            'active' => true
        ]);

        return true;
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

    private function processRow(array $row): bool
    {
        $code = $this->getVal($row, ['code_filiere', 'code filiere', 'code_filière', 'code filière']);
        $nom = $this->getVal($row, ['filiere', 'filière']);
        $niveau = $this->getVal($row, ['niveau']);
        $annee = $this->getVal($row, ['annee', 'année']);
        $secteur = $this->getVal($row, ['secteur']);
        $typeFormation = $this->getVal($row, ['type_de_formation', 'type de formation']);

        if (empty($code)) return false;

        // Check for duplicate by code
        if (Filiere::where('code', $code)->exists()) {
            return false;
        }

        Filiere::create([
            'code'           => $code,
            'nom'            => $nom ?? $code,
            'niveau'         => $this->mapNiveauImport($niveau),
            'annee'          => is_numeric($annee) ? (int) $annee : null,
            'secteur'        => $secteur,
            'type_formation' => $typeFormation,
            'active'         => true,
        ]);

        return true;
    }

    private function getVal(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && $row[$key] !== '') return trim($row[$key]);
            $alt = str_replace([' ', '_'], ['_', ' '], $key);
            if (isset($row[$alt]) && $row[$alt] !== '') return trim($row[$alt]);
        }
        return null;
    }

    private function mapNiveauImport(?string $value): string
    {
        if (empty($value)) return 'Technicien';
        $v = mb_strtolower(trim($value));
        if (str_contains($v, 'spécialisé') || str_contains($v, 'specialise') || $v === 'ts') return 'Technicien Spécialisé';
        if (str_contains($v, 'qualification') || $v === 'q') return 'Qualification';
        return 'Technicien';
    }
}

