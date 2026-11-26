<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        $years = AcademicYear::orderByDesc('starts_on')->get();

        return view('admin.years.index', [
            'years' => $years,
        ]);
    }

    public function create()
    {
        return view('admin.years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255', 'unique:academic_years,label'],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after:starts_on'],
            'is_active' => ['nullable', 'boolean'],
            'is_current' => ['nullable', 'boolean'],
            'external_id' => ['nullable', 'string', 'max:255'],
        ], [
            'label.required' => 'Le libellé de l\'année académique est obligatoire.',
            'label.unique' => 'Cette année académique existe déjà.',
            'starts_on.required' => 'La date de début est obligatoire.',
            'starts_on.date' => 'La date de début doit être une date valide.',
            'ends_on.required' => 'La date de fin est obligatoire.',
            'ends_on.date' => 'La date de fin doit être une date valide.',
            'ends_on.after' => 'La date de fin doit être postérieure à la date de début.',
        ]);

        $isActive = $request->boolean('is_active') || $request->boolean('is_current');

        DB::transaction(function () use ($validated, $isActive) {
            if ($isActive) {
                AcademicYear::query()->update(['is_active' => false]);
            }

            AcademicYear::create([
                'label' => trim($validated['label']),
                'starts_on' => $validated['starts_on'],
                'ends_on' => $validated['ends_on'],
                'is_active' => $isActive,
                'external_id' => $validated['external_id'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.years.index')
            ->with('success', 'Année académique créée avec succès.');
    }

    public function edit(AcademicYear $year)
    {
        return view('admin.years.edit', [
            'year' => $year,
        ]);
    }

    public function update(Request $request, AcademicYear $year)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255', Rule::unique('academic_years', 'label')->ignore($year->id)],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after:starts_on'],
            'is_active' => ['nullable', 'boolean'],
            'is_current' => ['nullable', 'boolean'],
            'external_id' => ['nullable', 'string', 'max:255'],
        ], [
            'label.required' => 'Le libellé de l\'année académique est obligatoire.',
            'label.unique' => 'Cette année académique existe déjà.',
            'starts_on.required' => 'La date de début est obligatoire.',
            'starts_on.date' => 'La date de début doit être une date valide.',
            'ends_on.required' => 'La date de fin est obligatoire.',
            'ends_on.date' => 'La date de fin doit être une date valide.',
            'ends_on.after' => 'La date de fin doit être postérieure à la date de début.',
        ]);

        $isActive = $request->boolean('is_active') || $request->boolean('is_current');

        DB::transaction(function () use ($year, $validated, $isActive) {
            if ($isActive) {
                AcademicYear::where('id', '!=', $year->id)->update(['is_active' => false]);
            }

            $year->update([
                'label' => trim($validated['label']),
                'starts_on' => $validated['starts_on'],
                'ends_on' => $validated['ends_on'],
                'is_active' => $isActive,
                'external_id' => $validated['external_id'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.years.index')
            ->with('success', 'Année académique mise à jour avec succès.');
    }
}
