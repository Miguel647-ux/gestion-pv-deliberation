<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1 DA / Administrateur (program_id = null)
        $da = User::updateOrCreate(
            ['email' => 'da@eneam.bj'],
            [
                'name'       => 'DA ENEAM',
                'password'   => bcrypt('password'),
                'role'       => 'da',
                'program_id' => null,
            ]
        );

        // 8 CD, un par filière (program_id renseigné)
        foreach (Program::orderBy('id')->get() as $program) {
            User::updateOrCreate(
                ['email' => 'cd.' . strtolower($program->code) . '@eneam.bj'],
                [
                    'name'       => 'CD ' . $program->name,
                    'password'   => bcrypt('password'),
                    'role'       => 'cd',
                    'program_id' => $program->id,
                ]
            );
        }

        $this->command->info('Comptes créés : DA = 1, CD = ' . Program::count());
        $this->command->info('Emails : da@eneam.bj + cd.<code>@eneam.bj (mot de passe : password)');
    }
}