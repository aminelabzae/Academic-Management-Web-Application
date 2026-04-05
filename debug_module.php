<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Module;
use App\Models\EmploiDuTemps;

// Check all modules and their hebdo hours
$modules = Module::with(['emploisDuTemps' => function($q) { $q->where('actif', true); }])->get();

echo "DEBUG MODULE HOURS:\n";
foreach ($modules as $m) {
    if ($m->emploisDuTemps->count() > 0) {
        echo "- Module: {$m->nom} (ID: {$m->id})\n";
        echo "  Emploi count: " . $m->emploisDuTemps->count() . "\n";
        $total = 0;
        foreach ($m->emploisDuTemps as $e) {
            $debut = \Carbon\Carbon::parse($e->heure_debut);
            $fin = \Carbon\Carbon::parse($e->heure_fin);
            $diff = $debut->diffInMinutes($fin) / 60;
            echo "    * EDT ID: {$e->id}, {$e->jour}, {$e->heure_debut}-{$e->heure_fin}, Duree: {$diff}h\n";
            $total += $diff;
        }
        echo "  CALCULATED HEBDO: {$total}h\n";
        echo "  METHOD HEBDO: " . $m->getHeuresHebdomadairesActuelles() . "h\n";
    }
}
