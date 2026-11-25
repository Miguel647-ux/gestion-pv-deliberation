<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\DeliberationPv;
use App\Models\Level;
use App\Models\Program;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoPvSeeder extends Seeder
{
    public function run(): void
    {
        $year    = AcademicYear::where('is_active', true)->firstOrFail();
        $ig      = Program::where('code', 'IG')->firstOrFail();
        $l3      = Level::where('code', 'L3')->firstOrFail();
        $s3      = Semester::where('code', 'S3')->firstOrFail();
        $cdIg    = User::where('role', 'cd')->where('program_id', $ig->id)->firstOrFail();

        $pdf = <<<PDF
%PDF-1.4
1 0 obj
<< /Type /Catalog /Pages 2 0 R >>
endobj
2 0 obj
<< /Type /Pages /Kids [3 0 R] /Count 1 >>
endobj
3 0 obj
<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] >>
endobj
xref
0 4
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
trailer
<< /Size 4 /Root 1 0 R >>
startxref
190
%%EOF
PDF;

        $pvs = [
    [
        'level_code'   => 'L2',
        'semester_code'=> 'S3',
        'class_name'   => 'L2 IG A',
        'specialty'    => null,
        'file'         => 'pvs/demo_l2.pdf',
    ],
    [
        'level_code'   => 'L3',
        'semester_code'=> 'S5',
        'class_name'   => 'L3 IG-ARI A',
        'specialty'    => 'ARI',
        'file'         => 'pvs/demo_ari.pdf',
    ],
    [
        'level_code'   => 'L3',
        'semester_code'=> 'S5',
        'class_name'   => 'L3 IG-AIP A',
        'specialty'    => 'AIP',
        'file'         => 'pvs/demo_aip.pdf',
    ],
];

        foreach ($pvs as $data) {
            Storage::disk('local')->put($data['file'], $pdf);

            DeliberationPv::updateOrCreate(
                [
                    'academic_year_id' => $year->id,
                    'program_id'       => $ig->id,
                    'level_id'         => $l3->id,
                    'semester_id'      => $s3->id,
                    'class_name'       => $data['class_name'],
                ],
                [
                    'specialty'         => $data['specialty'],
                    'deliberation_date' => '2026-07-15',
                    'file_path'         => $data['file'],
                    'file_name'         => basename($data['file']),
                    'file_type'         => 'application/pdf',
                    'file_size'         => strlen($pdf),
                    'status'            => DeliberationPv::STATUS_SOUMIS,
                    'created_by'        => $cdIg->id,
                ]
            );
        }

        $this->command->info('PV de démo créés : 2 (L3 IG ARI et L3 IG AIP)');
    }
}