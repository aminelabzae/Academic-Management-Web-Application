<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmploiDuTemps extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'emploi_du_temps';

    protected static function booted()
    {
        static::updated(function ($emploi) {
            if ($emploi->wasChanged('teams_link') && $emploi->teams_link) {
                $etudiants = Etudiant::where('groupe_id', $emploi->groupe_id)
                    ->whereNotNull('user_id')
                    ->get()
                    ->unique('user_id');
                
                $profName = $emploi->professeur ? ($emploi->professeur->nom_complet) : 'votre professeur';
                
                foreach ($etudiants as $etudiant) {
                    if ($etudiant->user) {
                        $etudiant->user->notify(new \App\Notifications\GenericNotification("🔗 Le lien Teams pour la séance de {$emploi->module->nom} du {$emploi->jour} ({$emploi->heure_debut}) a été mis à jour par {$profName}."));
                    }
                }
            }
        });
    }

    protected $fillable = [
        'jour',
        'heure_debut',
        'heure_fin',
        'professeur_id',
        'groupe_id',
        'module_id',
        'salle_id',
        'type_seance',
        'teams_link',
        'semaine_type',
        'statut_approbation',
        'motif_annulation',
        'is_examen',
        'date_debut_validite',
        'date_fin_validite',
        'actif'
    ];

    protected $casts = [
        'date_debut_validite' => 'date',
        'date_fin_validite' => 'date',
        'actif' => 'boolean'
    ];

    // Relations
    public function professeur()
    {
        return $this->belongsTo(Professeur::class);
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }

    public function realisations()
    {
        return $this->hasMany(SeanceRealisation::class, 'emploi_du_temps_id');
    }

    /**
     * Vérifier si la séance a été réalisée pour une date donnée
     */
    public function isRealisee(?string $date = null): bool
    {
        $date = $date ?: now()->toDateString();
        return $this->realisations()->where('date', $date)->exists();
    }

    /**
     * Obtenir tous les modules que ce professeur enseigne à ce groupe
     */
    public function getModulesDisponibles()
    {
        return Module::whereHas('emploisDuTemps', function($q) {
            $q->where('professeur_id', $this->professeur_id)
              ->where('groupe_id', $this->groupe_id)
              ->where('actif', true);
        })->get();
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeJour($query, string $jour)
    {
        return $query->where('jour', $jour);
    }

    public function scopePourProfesseur($query, int $professeurId)
    {
        return $query->where('professeur_id', $professeurId);
    }

    public function scopePourGroupe($query, int $groupeId)
    {
        return $query->where('groupe_id', $groupeId);
    }

    // Accesseurs
    public function getHoraireAttribute(): string
    {
        return $this->heure_debut . ' - ' . $this->heure_fin;
    }

    // Constantes
    const JOURS = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

    public static function getCreneaux(string $jour, bool $isRamadan = false): array
    {
        if ($isRamadan) {
            if ($jour === 'Vendredi') {
                return [
                    '08:30' => '10:15',
                    '10:15' => '12:15',
                    '13:15' => '14:45',
                    '14:45' => '16:30',
                ];
            }
            return [
                '08:30' => '10:30',
                '10:30' => '12:30',
                '12:30' => '14:30',
                '14:30' => '16:30',
            ];
        }

        if ($jour === 'Vendredi') {
            return [
                '08:20' => '10:20',
                '10:30' => '12:30',
                '14:30' => '16:20',
                '16:30' => '18:30',
            ];
        }

        return [
            '08:30' => '11:00',
            '11:00' => '13:30',
            '13:30' => '16:00',
            '16:00' => '18:30',
        ];
    }

    public static function formatHeures(float $heures): string
    {
        $h = floor($heures);
        $m = round(($heures - $h) * 60);

        if ($m == 0) {
            return "{$h}h";
        }

        return "{$h}h" . str_pad($m, 2, '0', STR_PAD_LEFT) . "min";
    }

    const SEMAINE_TYPES = ['Toutes', 'Paire', 'Impaire'];
}
