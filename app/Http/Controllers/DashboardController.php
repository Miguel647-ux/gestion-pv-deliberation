<?php

namespace App\Http\Controllers;

use App\Models\DeliberationPv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $baseQuery = DeliberationPv::query()->visibleTo($user);

        $total = (clone $baseQuery)->count();
        $soumis = (clone $baseQuery)->where('status', DeliberationPv::STATUS_SOUMIS)->count();
        $archive = (clone $baseQuery)->where('status', DeliberationPv::STATUS_ARCHIVE)->count();

        $parFiliere = (clone $baseQuery)
            ->join('programs', 'deliberation_pvs.program_id', '=', 'programs.id')
            ->select('programs.id', 'programs.code', 'programs.name', DB::raw('COUNT(*) as total'))
            ->groupBy('programs.id', 'programs.code', 'programs.name')
            ->orderBy('programs.name')
            ->get();

        $parAnnee = (clone $baseQuery)
            ->join('academic_years', 'deliberation_pvs.academic_year_id', '=', 'academic_years.id')
            ->select('academic_years.id', 'academic_years.label', DB::raw('COUNT(*) as total'))
            ->groupBy('academic_years.id', 'academic_years.label')
            ->orderByDesc('academic_years.id')
            ->get();

        $latest = DeliberationPv::query()
            ->visibleTo($user)
            ->with(['program', 'level', 'semester', 'academicYear'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', [
            'stats' => [
                'total' => $total,
                'soumis' => $soumis,
                'archive' => $archive,
                'parFiliere' => $parFiliere,
                'parAnnee' => $parAnnee,
            ],
            'latest' => $latest,
        ]);
    }
}
