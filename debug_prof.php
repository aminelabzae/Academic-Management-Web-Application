<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EmploiDuTemps;
use App\Models\SeanceRealisation;

$profId = 32;
$emplois = App\Models\EmploiDuTemps::where('professeur_id', $profId)->get();
echo "EmploiDuTemps for Prof $profId (" . $emplois->count() . " records):\n";
foreach ($emplois as $e) {
    echo "- ID: {$e->id}, {$e->jour}, Type: {$e->semaine_type}, Actif: " . ($e->actif ? '1' : '0') . ", Heures: {$e->heure_debut}-{$e->heure_fin}\n";
}

$realisationsCount = App\Models\SeanceRealisation::where('professeur_id', $profId)->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])->get();
echo "\nSeanceRealisation count for this month: " . $realisationsCount->count() . "\n";
foreach ($realisationsCount as $r) {
    echo "- ID: {$r->id}, Date: {$r->date->toDateString()}, Duree: {$r->duree_minutes}, EmploiID: {$r->emploi_du_temps_id}\n";
}

$realisations = SeanceRealisation::where('professeur_id', $profId)->get();
echo "\nSeanceRealisation for Prof $profId:\n";
foreach ($realisations as $r) {
    echo "- ID: {$r->id}, Date: {$r->date}, Duree: {$r->duree_minutes}, EmploiID: {$r->emploi_du_temps_id}\n";
}
