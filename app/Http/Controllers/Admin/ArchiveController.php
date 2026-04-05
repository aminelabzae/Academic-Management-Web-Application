<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeanceRealisation;
use App\Models\Professeur;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        // On récupère toutes les réalisations groupées par mois
        $realisations = SeanceRealisation::select(
            DB::raw('YEAR(date) as year'),
            DB::raw('MONTH(date) as month'),
            DB::raw('COUNT(*) as total_sessions'),
            DB::raw('SUM(duree_minutes) as total_minutes')
        )
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->paginate(12);

        return view('admin.archives.index', compact('realisations'));
    }

    public function show($year, $month)
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        $realisations = SeanceRealisation::with(['professeur', 'groupe', 'module', 'emploiDuTemps'])
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('date')
            ->get();

        $statsProf = SeanceRealisation::select('professeur_id', DB::raw('SUM(duree_minutes) as total_minutes'))
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->groupBy('professeur_id')
            ->with('professeur')
            ->get();

        return view('admin.archives.show', compact('realisations', 'statsProf', 'year', 'month', 'startOfMonth', 'endOfMonth'));
    }
}
