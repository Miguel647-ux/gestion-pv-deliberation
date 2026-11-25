<?php
namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            ['code' => 'IG',  'name' => 'Informatique de Gestion'],
            ['code' => 'PLAN',  'name' => 'Planification'],
            ['code' => 'GCO', 'name' => 'Gestion Commerciale'],
            ['code' => 'GBA',  'name' => 'Gestion Banque et Assurance'],
            ['code' => 'GFC',  'name' => 'Gestion Financière et Comptable'],
            ['code' => 'GRH', 'name' => 'Gestion des Ressources Humaines'],
            ['code' => 'STAT',  'name' => 'Statistique'],
            ['code' => 'GTL',  'name' => 'Gestion Transport et Logistique'],
        ];

        foreach ($programs as $program) {
            Program::updateOrCreate(['code' => $program['code']], $program);
        }
    }
}
// TODO: remplacer ce tableau par un import depuis la BD de l'école.
// Colonnes cibles : code, name, external_id.