<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Professeur;
use App\Models\Filiere;
use App\Models\SeanceRealisation;
use App\Models\SyllabusItem;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',        // Code unique du module (ex: INF101)
        'nom',         // Nom du module
        'masse_horaire', // Masse horaire
        'coefficient', // Coefficient
        'semestre',    // Semestre
    ];

    protected $casts = [
        'masse_horaire' => 'integer',
    ];

    // Un module peut être enseigné par plusieurs professeurs
    public function professeurs()
    {
        return $this->belongsToMany(Professeur::class, 'module_professeur');
    }

    // Un module peut appartenir à plusieurs filières
    public function filieres()
    {
        return $this->belongsToMany(Filiere::class);
    }

    // Un module peut apparaître dans plusieurs emplois du temps
    public function emploisDuTemps()
    {
        return $this->hasMany(EmploiDuTemps::class);
    }

    /**
     * Calculer les heures mensuelles réalisées du module pour un groupe spécifique (ou tous)
     */
    public function getHeuresMensuellesActuelles(?int $excludeEmploiId = null, ?int $groupeId = null): float
    {
        $debutMois = now()->startOfMonth();
        $finMois = now()->endOfMonth();

        $query = SeanceRealisation::where('module_id', $this->id);

        if ($groupeId) {
            $query->whereHas('emploiDuTemps', function($q) use ($groupeId) {
                $q->where('groupe_id', $groupeId);
            });
        }

        if ($excludeEmploiId) {
            $query->where('emploi_du_temps_id', '!=', $excludeEmploiId);
        }

        $query->whereBetween('date', [$debutMois, $finMois]);

        $totalMinutes = 0;
        foreach ($query->get() as $realisation) {
            $totalMinutes += $realisation->duree_minutes;
        }

        return $totalMinutes / 60;
    }

    /**
     * Calculer le total des heures (masse horaire consommée) pour un groupe
     * Basé sur les séances RÉELLEMENT validées par les professeurs
     */
    public function getHeuresTotalesByGroupe(?int $groupeId = null, ?int $excludeEmploiId = null): float
    {
        $query = SeanceRealisation::where('module_id', $this->id);

        if ($groupeId !== null && $groupeId > 0) {
            $query->whereHas('emploiDuTemps', function($q) use ($groupeId) {
                $q->where('groupe_id', $groupeId);
            });
        }

        if ($excludeEmploiId) {
            $query->where('emploi_du_temps_id', '!=', $excludeEmploiId);
        }

        $totalMinutes = 0;
        foreach ($query->get() as $realisation) {
            $totalMinutes += $realisation->duree_minutes;
        }

        return $totalMinutes / 60;
    }

    public function getHeuresHebdomadairesByGroupe(int $groupeId): float
    {
        $emplois = $this->emploisDuTemps()
            ->where('actif', true)
            ->where('groupe_id', $groupeId)
            ->get();
            
        $totalMinutes = 0;
        foreach ($emplois as $emploi) {
            $debut = \Carbon\Carbon::parse($emploi->heure_debut);
            $fin = \Carbon\Carbon::parse($emploi->heure_fin);
            $totalMinutes += $debut->diffInMinutes($fin);
        }
        return $totalMinutes / 60;
    }

    public function getHeuresHebdomadairesActuelles(): float
    {
        $emplois = $this->emploisDuTemps()->where('actif', true)->get();
        $totalMinutes = 0;
        foreach ($emplois as $emploi) {
            $debut = \Carbon\Carbon::parse($emploi->heure_debut);
            $fin = \Carbon\Carbon::parse($emploi->heure_fin);
            $totalMinutes += $debut->diffInMinutes($fin);
        }
        return $totalMinutes / 60;
    }

    public function syllabusItems()
    {
        return $this->hasMany(SyllabusItem::class)->orderBy('ordre');
    }

    public function getProgressSyllabusAttribute()
    {
        $totalPoids = $this->syllabusItems()->sum('poids_pourcentage');
        if ($totalPoids == 0) return 0;

        $completedPoids = $this->syllabusItems()
            ->whereHas('realisations')
            ->sum('poids_pourcentage');
        
        return min(100, round(($completedPoids / $totalPoids) * 100));
    }
}
