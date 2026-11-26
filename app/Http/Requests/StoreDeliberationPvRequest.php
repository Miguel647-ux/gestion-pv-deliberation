<?php

namespace App\Http\Requests;

use App\Models\DeliberationPv;
use App\Models\Level;
use App\Models\Program;
use App\Models\Semester;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreDeliberationPvRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', DeliberationPv::class) ?? false;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $user = $this->user();

        if ($user && ! $user->isDa()) {
            $this->merge([
                'program_id' => $user->program_id,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'program_id' => [
                Rule::requiredIf(fn () => (bool) $this->user()?->isDa()),
                'integer',
                'exists:programs,id',
            ],
            'level_id' => ['required', 'integer', 'exists:levels,id'],
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'specialty' => ['nullable', 'string', Rule::in(DeliberationPv::SPECIALTIES_IG)],
            'deliberation_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.class_name' => ['required', 'string', 'max:100', 'distinct'],
            'items.*.file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $user = $this->user();
                $programId = $user?->isDa() ? $this->input('program_id') : $user?->program_id;
                $program = $programId ? Program::find($programId) : null;
                $level = $this->filled('level_id') ? Level::find($this->input('level_id')) : null;
                $semester = $this->filled('semester_id') ? Semester::find($this->input('semester_id')) : null;

                // 1. Cohérence niveau / semestre
                if ($level && $semester && ! DeliberationPv::semesterApplies($level->code, $semester->code)) {
                    $validator->errors()->add(
                        'semester_id',
                        'Le semestre sélectionné ne correspond pas au niveau choisi.'
                    );
                }

                // 2. Cohérence de la spécialité (uniquement filière IG en L3)
                if ($this->filled('specialty')) {
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

                // 3. Unicité des noms de classe dans le lot et en base de données
                $items = $this->input('items');
                if (is_array($items)) {
                    $classNamesMap = [];
                    foreach ($items as $index => $item) {
                        if (isset($item['class_name']) && is_string($item['class_name'])) {
                            $cleaned = trim($item['class_name']);
                            if ($cleaned !== '') {
                                $classNamesMap[$index] = $cleaned;
                            }
                        }
                    }

                    // Doublons au sein du même lot (insensible à la casse)
                    $seen = [];
                    foreach ($classNamesMap as $index => $className) {
                        $normalized = mb_strtolower($className);
                        if (in_array($normalized, $seen, true)) {
                            $validator->errors()->add(
                                "items.{$index}.class_name",
                                "La classe \"{$className}\" est présente plusieurs fois dans ce lot."
                            );
                        } else {
                            $seen[] = $normalized;
                        }
                    }

                    // Doublons existants en base (academic_year_id + program_id + level_id + semester_id + class_name)
                    $academicYearId = $this->input('academic_year_id');
                    $levelId = $this->input('level_id');
                    $semesterId = $this->input('semester_id');

                    if ($academicYearId && $programId && $levelId && $semesterId && ! empty($classNamesMap)) {
                        $existingInDb = DeliberationPv::query()
                            ->where('academic_year_id', $academicYearId)
                            ->where('program_id', $programId)
                            ->where('level_id', $levelId)
                            ->where('semester_id', $semesterId)
                            ->whereIn('class_name', array_values($classNamesMap))
                            ->pluck('class_name')
                            ->map(fn ($name) => mb_strtolower($name))
                            ->all();

                        foreach ($classNamesMap as $index => $className) {
                            if (in_array(mb_strtolower($className), $existingInDb, true)) {
                                $validator->errors()->add(
                                    "items.{$index}.class_name",
                                    "Un PV pour la classe \"{$className}\" existe déjà pour cette année académique, filière, niveau et semestre."
                                );
                            }
                        }
                    }
                }
            },
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'academic_year_id.required' => 'L\'année académique est obligatoire.',
            'academic_year_id.exists' => 'L\'année académique sélectionnée est invalide.',
            'program_id.required' => 'La filière est obligatoire.',
            'program_id.exists' => 'La filière sélectionnée est invalide.',
            'level_id.required' => 'Le niveau est obligatoire.',
            'level_id.exists' => 'Le niveau sélectionné est invalide.',
            'semester_id.required' => 'Le semestre est obligatoire.',
            'semester_id.exists' => 'Le semestre sélectionné est invalide.',
            'specialty.in' => 'La spécialité sélectionnée est invalide (ARI ou AIP uniquement).',
            'deliberation_date.required' => 'La date de délibération est obligatoire.',
            'deliberation_date.date' => 'La date de délibération doit être une date valide.',
            'items.required' => 'Vous devez fournir au moins un fichier PV.',
            'items.array' => 'Le format des PVs soumis est invalide.',
            'items.min' => 'Vous devez fournir au moins un fichier PV.',
            'items.*.class_name.required' => 'Le nom de la classe est obligatoire pour chaque PV.',
            'items.*.class_name.max' => 'Le nom de la classe ne doit pas dépasser 100 caractères.',
            'items.*.class_name.distinct' => 'Chaque classe ne peut apparaître qu\'une seule fois dans le même lot.',
            'items.*.file.required' => 'Le fichier PV est obligatoire pour chaque classe.',
            'items.*.file.file' => 'Chaque élément doit être un fichier valide.',
            'items.*.file.mimes' => 'Le fichier doit être au format PDF, JPG, JPEG ou PNG.',
            'items.*.file.max' => 'Le fichier ne doit pas dépasser 5 Mo (5120 Ko).',
        ];
    }
}
