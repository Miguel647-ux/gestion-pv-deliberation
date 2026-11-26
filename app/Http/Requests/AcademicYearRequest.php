<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isDa() ?? false;
    }

    public function rules(): array
    {
        $yearId = $this->route('year')?->id;

        return [
            'label'     => ['required', 'string', 'max:20', 'regex:/^\d{4}-\d{4}$/', Rule::unique('academic_years', 'label')->ignore($yearId)],
            'starts_on' => ['required', 'date'],
            'ends_on'   => ['required', 'date', 'after:starts_on'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'label.regex'      => 'Le libellé doit être au format 2025-2026.',
            'label.unique'     => 'Cette année académique existe déjà.',
            'ends_on.after'    => 'La date de fin doit être postérieure à la date de début.',
        ];
    }
}