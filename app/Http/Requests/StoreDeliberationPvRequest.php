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
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isDa() || $this->user()->isCd());
    }

    protected function prepareForValidation(): void
    {
        $user = $this->user();

        // Un CD ne peut déposer que pour sa propre filière
        if ($user && ! $user->isDa()) {
            $this->merge([
                'program_id' => $user->program_id,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'program_id'       => ['required', 'integer', 'exists:programs,id'],
            'level_id'         => ['required', 'integer', 'exists:levels,id'],
            'semester_id'      => ['required', 'integer', 'exists:semesters,id'],
            'specialty'        => ['nullable', 'string', Rule::in(DeliberationPv::SPECIALTIES_IG)],
            'deliberation_date'=> ['required', 'date'],

            // Dépôt multi-fichiers
            'items'               => ['required', 'array', 'min:1'],
            'items.*.class_name'  => ['required', 'string', 'max:100'],
            'items.*.file'        => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function after(): array
{
    return [
        function (Validator $validator) {
            // 1. Cohérence niveau/semestre
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

            // 2. Spécialité
            if ($this->filled('specialty')) {
                $programId = $this->input('program_id');
                $program = $programId ? Program::find($programId) : null;

                if ($program && $program->code !== DeliberationPv::IG_CODE) {
                    $validator->errors()->add(
                        'specialty',
                        "La spécialité n'est autorisée que pour la filière Informatique de gestion (IG)."
                    );
                } elseif ($level && ! in_array($level->code, DeliberationPv::SPECIALTY_LEVELS, true)) {
                    $validator->errors()->add(
                        'specialty',
                        "La spécialité ne s'applique qu'au niveau Licence 3 (L3)."
                    );
                }
            }

            // 3. Doublons DANS LE LOT (class_name + toutes les clés métier)
            $items = $this->input('items', []);
            if (! is_array($items)) {
                return;
            }

            $seen = [];
            foreach ($items as $index => $item) {
                $name = trim((string) ($item['class_name'] ?? ''));
                if ($name === '') {
                    continue;
                }

                if (isset($seen[$name])) {
                    $validator->errors()->add(
                        "items.$index.class_name",
                        "Le nom de classe « $name » est déjà utilisé dans ce lot. Chaque PV doit avoir une classe unique."
                    );
                }
                $seen[$name] = true;
            }
        },
    ];
}
}