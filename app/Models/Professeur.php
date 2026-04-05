<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Etudiant;
use App\Notifications\LinkUpdatedNotification;

class Professeur extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'email',
        'telephone',
        'specialite',
        'user_id',
        'actif',
        'max_heures_mensuel',
        'taux_horaire'
    ];

    protected $casts = [
        'actif' => 'boolean',
        'max_heures_mensuel' => 'integer',
        'taux_horaire' => 'float'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function emploisDuTemps()
    {
        return $this->hasMany(EmploiDuTemps::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'module_professeur');
    }

    public function groupes()
    {
        return $this->belongsToMany(Groupe::class, 'professeur_groupe');
    }

    public function filieres()
    {
        return $this->belongsToMany(Filiere::class, 'filiere_professeur');
    }

    // Accesseurs
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Calculer les heures hebdomadaires actuelles du professeur (Masse horaire)
     */
    public function getHeuresHebdomadairesActuelles(?int $excludeEmploiId = null): float
    {
        $query = $this->emploisDuTemps()->where('actif', true);

        if ($excludeEmploiId) {
            $query->where('id', '!=', $excludeEmploiId);
        }

        $total = 0;
        foreach ($query->get() as $emploi) {
            $debut = \Carbon\Carbon::parse($emploi->heure_debut);
            $fin = \Carbon\Carbon::parse($emploi->heure_fin);
            $heures = $debut->diffInMinutes($fin) / 60;
            
            // Si c'est 'Toutes', on compte la séance une fois par semaine
            // Si c'est Paire/Impaire, on compte 0.5 (en moyenne par semaine) ou on reste sur l'hebdo simple
            // Pour la masse horaire hebdomadaire standard, on compte chaque séance de l'emploi du temps
            $total += $heures;
        }

        return $total;
    }

    /**
     * Calculer les heures mensuelles actuelles du professeur (Estimation)
     * Chaque créneau = 2h30 (2.5h)
     * semaine_type 'Toutes' = ×4/mois, 'Paire'/'Impaire' = ×2/mois
     */
    public function getHeuresMensuellesActuelles(?int $excludeEmploiId = null): float
    {
        $debutMois = now()->startOfMonth();
        $finMois = now()->endOfMonth();
        
        // On compte les séances explicitement créées pour ce professeur dans le mois
        $query = SeanceRealisation::where('professeur_id', $this->id)
            ->whereBetween('date', [$debutMois, $finMois]);

        if ($excludeEmploiId) {
            $query->where('emploi_du_temps_id', '!=', $excludeEmploiId);
        }

        $totalMinutes = 0;
        foreach ($query->get() as $realisation) {
            $totalMinutes += $realisation->duree_minutes;
        }

        return $totalMinutes / 60;
    }

    /**
     * Calculer les heures mensuelles réalisées du professeur
     */
    public function getHeuresMensuellesRealisees(?int $moduleId = null): float
    {
        $debutMois = now()->startOfMonth();
        $finMois = now()->endOfMonth();

        $query = SeanceRealisation::whereHas('emploiDuTemps', function($q) {
            $q->where('professeur_id', $this->id)
              ->whereNull('deleted_at'); // Exclure les séances supprimées
        })
        ->whereBetween('date', [$debutMois, $finMois]);

        if ($moduleId) {
            $query->where(function($q) use ($moduleId) {
                $q->where('module_id', $moduleId)
                  ->orWhere(function($sq) use ($moduleId) {
                      $sq->whereNull('module_id')
                         ->whereHas('emploiDuTemps', function($eq) use ($moduleId) {
                             $eq->where('module_id', $moduleId);
                         });
                  });
            });
        }

        $totalMinutes = 0;
        foreach ($query->get() as $realisation) {
            $emploi = $realisation->emploiDuTemps;
            $debut = \Carbon\Carbon::parse($emploi->heure_debut);
            $fin = \Carbon\Carbon::parse($emploi->heure_fin);
            $totalMinutes += $debut->diffInMinutes($fin);
        }

        return $totalMinutes / 60;
    }

    /**
     * Obtenir les statistiques des heures réalisées par module pour le mois en cours
     */
    public function getStatistiquesHeuresParModule(): array
    {
        $modules = Module::whereHas('emploisDuTemps', function($q) {
            $q->where('professeur_id', $this->id)->where('actif', true);
        })->get();

        $stats = [];

        foreach ($modules as $module) {
            $heuresRealisees = $this->getHeuresMensuellesRealisees($module->id);
            
            $stats[] = [
                'nom' => $module->nom,
                'code' => $module->code,
                'heures_mensuelles' => $heuresRealisees,
                'max_heures' => $module->max_heures_mensuel,
            ];
        }

        return $stats;
    }
}
