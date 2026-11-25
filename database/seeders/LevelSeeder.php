<?php
namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['code' => 'L1', 'name' => 'Licence 1'],
            ['code' => 'L2', 'name' => 'Licence 2'],
            ['code' => 'L3', 'name' => 'Licence 3'],
            ['code' => 'M1', 'name' => 'Master 1'],
            ['code' => 'M2', 'name' => 'Master 2'],
        ];

        foreach ($levels as $level) {
            Level::updateOrCreate(['code' => $level['code']], $level);
        }
    }
}
// TODO: remplacer ce tableau par un import depuis la BD de l'école.
// Colonnes cibles : code, name, external_id.