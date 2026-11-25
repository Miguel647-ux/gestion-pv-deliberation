<?php
namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        $semesters = [
            ['code' => 'S1', 'name' => 'Semestre 1'],
            ['code' => 'S2', 'name' => 'Semestre 2'],
            ['code' => 'S3', 'name' => 'Semestre 3'],
            ['code' => 'S4', 'name' => 'Semestre 4'],
            ['code' => 'S5', 'name' => 'Semestre 5'],
        ];

        foreach ($semesters as $semester) {
            Semester::updateOrCreate(['code' => $semester['code']], $semester);
        }
    }
}
// TODO: remplacer ce tableau par un import depuis la BD de l'école.
// Colonnes cibles : code, name, external_id.