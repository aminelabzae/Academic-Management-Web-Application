<?php

namespace App\Http\Controllers\Professeur;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Professeur;
use App\Models\SyllabusItem;
use Illuminate\Http\Request;

class SyllabusController extends Controller
{
    public function store(Request $request, Module $module)
    {
        $user = auth()->user();
        $professeur = Professeur::where('user_id', $user->id)->first();

        // Sécurité : Vérifier que le module appartient bien à ce professeur via la table pivot
        if (!$professeur || !$professeur->modules->contains($module->id)) {
            return response()->json(['error' => 'Action non autorisée. Vous n\'êtes pas assigné à ce module.'], 403);
        }

        $request->validate([
            'titre' => 'required|string|max:255',
            'poids_pourcentage' => 'required|integer|min:0|max:100',
        ]);

        // Calculer l'ordre (dernier + 1)
        $dernierOrdre = $module->syllabusItems()->max('ordre') ?? 0;

        $item = $module->syllabusItems()->create([
            'titre' => $request->titre,
            'poids_pourcentage' => $request->poids_pourcentage,
            'ordre' => $dernierOrdre + 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'item' => $item,
                'message' => 'Chapitre ajouté avec succès.'
            ]);
        }

        return back()->with('success', 'Chapitre ajouté au syllabus.');
    }
}
