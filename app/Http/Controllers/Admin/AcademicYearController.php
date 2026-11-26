<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(): View
    {
        $years = AcademicYear::withCount('deliberationPvs')
            ->orderByDesc('label')
            ->get();

        return view('admin.years.index', compact('years'));
    }

    public function create(): View
    {
        return view('admin.years.create');
    }

    public function store(AcademicYearRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['is_active'])) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicYear::create($data);

        return redirect()
            ->route('admin.years.index')
            ->with('status', 'Année académique créée.');
    }

    public function show(AcademicYear $year): RedirectResponse
    {
        return redirect()->route('admin.years.edit', $year);
    }

    public function edit(AcademicYear $year): View
    {
        return view('admin.years.edit', compact('year'));
    }

    public function update(AcademicYearRequest $request, AcademicYear $year): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['is_active'])) {
            AcademicYear::where('is_active', true)
                ->where('id', '!=', $year->id)
                ->update(['is_active' => false]);
        }

        $year->update($data);

        return redirect()
            ->route('admin.years.index')
            ->with('status', 'Année académique mise à jour.');
    }

    public function destroy(AcademicYear $year): RedirectResponse
    {
        if ($year->deliberationPvs()->exists()) {
            return back()->with('error', 'Impossible de supprimer : des PV sont rattachés à cette année.');
        }

        $year->delete();

        return redirect()
            ->route('admin.years.index')
            ->with('status', 'Année académique supprimée.');
    }
}