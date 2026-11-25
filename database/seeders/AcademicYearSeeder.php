<?php
namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        AcademicYear::updateOrCreate(
            ['label' => '2025-2026'],
            [
                'starts_on' => '2025-10-01',
                'ends_on'   => '2026-07-31',
                'is_active' => true,
            ]
        );
    }
}
// TODO: remplacer ce tableau par un import depuis la BD de l'école.
// Colonnes cibles : code, name, external_id.