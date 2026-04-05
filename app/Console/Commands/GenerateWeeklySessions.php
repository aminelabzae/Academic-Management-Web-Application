<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmploiDuTemps;
use App\Models\SeanceRealisation;
use Carbon\Carbon;

class GenerateWeeklySessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-weekly-sessions {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Génère les instances de séances pour une semaine spécifique à partir du modèle d\'emploi du temps';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startDate = $this->argument('date') ? Carbon::parse($this->argument('date'))->startOfWeek() : now()->startOfWeek();
        $this->info("Génération des séances pour la semaine du " . $startDate->format('d/m/Y'));

        $emplois = EmploiDuTemps::where('actif', true)->get();

        $daysMapping = [
            'Lundi' => 0,
            'Mardi' => 1,
            'Mercredi' => 2,
            'Jeudi' => 3,
            'Vendredi' => 4,
            'Samedi' => 5,
        ];

        $count = 0;
        foreach ($emplois as $emploi) {
            if (!isset($daysMapping[$emploi->jour])) continue;

            $date = $startDate->copy()->addDays($daysMapping[$emploi->jour]);
            
            // Si c'est semaine Paire/Impaire, vérifier la correspondance
            if ($emploi->semaine_type !== 'Toutes') {
                $isWeekPaire = $date->weekOfYear % 2 === 0;
                if ($emploi->semaine_type === 'Paire' && !$isWeekPaire) continue;
                if ($emploi->semaine_type === 'Impaire' && $isWeekPaire) continue;
            }

            $existing = SeanceRealisation::where([
                'emploi_du_temps_id' => $emploi->id,
                'date' => $date->toDateString(),
            ])->first();

            if (!$existing || $existing->statut === 'prevue') {
                $debut = Carbon::parse($emploi->heure_debut);
                $fin = Carbon::parse($emploi->heure_fin);
                $duree = $debut->diffInMinutes($fin);

                SeanceRealisation::updateOrCreate([
                    'emploi_du_temps_id' => $emploi->id,
                    'date' => $date->toDateString(),
                ], [
                    'module_id' => $emploi->module_id,
                    'professeur_id' => $emploi->professeur_id,
                    'groupe_id' => $emploi->groupe_id,
                    'duree_minutes' => $duree,
                    'statut' => 'prevue'
                ]);
                $count++;
            }
        }

        $this->info("Succès : {$count} séances générées/mises à jour.");
    }
}
