<?php

namespace App\Http\Requests;

use App\Models\DeliberationPv;
use App\Models\Level;
use App\Models\Semester;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateDeliberationPvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        /** @var DeliberationPv|null $pv */
        $pv = $this->route('pv');
        $user = $this->user();

        if ($user && ! $user->isDa()) {
            $this->merge([
                'program_id' => $pv?->program_id ?? $user->program_id,
            ]);
        }
    }

    public function rules(): array
    {
        /** @var DeliberationPv $pv */
        $pv = $this->route('pv');

        $academicYearId = $this->input('academic_year_id', $pv->academic_year_id);
        $programId = $this->input('program_id', $pv->program_id);
        $levelId = $this->input('level_id', $pv->level_id);
        $semesterId = $this->input('semester_id', $pv->semester_id);

        return [
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'level_id' => ['required', 'integer', 'exists:levels,id'],
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'specialty' => ['nullable', 'string', Rule::in(DeliberationPv::SPECIALTIES_IG)],
            'deliberation_date' => ['required', 'date'],
            'class_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('deliberation_pvs', 'class_name')
                    ->where(function ($query) use ($academicYearId, $programId, $levelId, $semesterId) {
                        return $query
                            ->where('academic_year_id', $academicYearId)
                            ->where('program_id', $programId)
                            ->where('level_id', $levelId)
                            ->where('semester_id', $semesterId);
                    })
                    ->ignore($pv->id),
            ],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->has('level_id') || $validator->errors()->has('semester_id')) {
                    return;
                }

                $level = Level::find($this->input('level_id'));
                $semester = Semester::find($this->input('semester_id'));

                if (! $level || ! $semester || ! DeliberationPv::semesterApplies($level->code, $semester->code)) {
                    $validator->errors()->add(
                        'semester_id',
                        'Le semestre sélectionné ne correspond pas au niveau choisi.'
                    );
                }

                if ($this->filled('specialty')) {
                    $user = $this->user();
                    $programId = $user?->isDa() ? $this->input('program_id') : $this->route('pv')?->program_id;
                    $program = $programId ? \App\Models\Program::find($programId) : null;

                    if ($program && $program->code !== DeliberationPv::IG_CODE) {
                        $validator->errors()->add(
                            'specialty',
                            'La spécialité n\'est autorisée que pour la filière Informatique de gestion (IG).'
                        );
                    } elseif ($level && ! in_array($level->code, DeliberationPv::SPECIALTY_LEVELS, true)) {
                        $validator->errors()->add(
                            'specialty',
                            'La spécialité ne s\'applique qu\'au niveau Licence 3 (L3).'
                        );
                    }
                }
            },
        ];
    }
}