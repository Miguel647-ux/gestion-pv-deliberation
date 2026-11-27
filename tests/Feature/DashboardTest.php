<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\DeliberationPv;
use App\Models\Level;
use App\Models\Program;
use App\Models\Semester;
use App\Models\User;
use Database\Seeders\AcademicYearSeeder;
use Database\Seeders\LevelSeeder;
use Database\Seeders\ProgramSeeder;
use Database\Seeders\SemesterSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $da;
    private User $cdIg;
    private User $cdFc;
    private Program $ig;
    private Program $fc;
    private AcademicYear $year;
    private Level $l2;
    private Semester $s3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            AcademicYearSeeder::class,
            ProgramSeeder::class,
            LevelSeeder::class,
            SemesterSeeder::class,
            UserSeeder::class,
        ]);

        Storage::fake('local');

        $this->year = AcademicYear::where('is_active', true)->firstOrFail();
        $this->ig = Program::where('code', 'IG')->firstOrFail();
        $this->fc = Program::where('code', 'GFC')->firstOrFail();
        $this->l2 = Level::where('code', 'L2')->firstOrFail();
        $this->s3 = Semester::where('code', 'S3')->firstOrFail();

        $this->da = User::where('role', 'da')->firstOrFail();
        $this->cdIg = User::where('role', 'cd')->where('program_id', $this->ig->id)->firstOrFail();
        $this->cdFc = User::where('role', 'cd')->where('program_id', $this->fc->id)->firstOrFail();
    }

    private function createPv(Program $program, string $className, string $status = DeliberationPv::STATUS_SOUMIS): DeliberationPv
    {
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
        $stored = $file->store('private/pvs', 'local');

        return DeliberationPv::create([
            'academic_year_id' => $this->year->id,
            'program_id' => $program->id,
            'level_id' => $this->l2->id,
            'semester_id' => $this->s3->id,
            'class_name' => $className,
            'deliberation_date' => '2026-07-01',
            'file_path' => $stored,
            'file_name' => 'test.pdf',
            'file_type' => 'application/pdf',
            'file_size' => 100,
            'status' => $status,
            'created_by' => $this->cdIg->id,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_da_can_view_dashboard_with_all_stats(): void
    {
        $this->createPv($this->ig, 'L2 IG Alpha', DeliberationPv::STATUS_SOUMIS);
        $this->createPv($this->fc, 'L2 GFC Beta', DeliberationPv::STATUS_ARCHIVE);

        $response = $this->actingAs($this->da)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Tableau de bord');
        $response->assertSee('L2 IG Alpha');
        $response->assertSee('L2 GFC Beta');
    }

    public function test_cd_sees_filtered_stats_for_their_program(): void
    {
        $this->createPv($this->ig, 'L2 IG Alpha', DeliberationPv::STATUS_SOUMIS);
        $this->createPv($this->fc, 'L2 GFC Beta', DeliberationPv::STATUS_ARCHIVE);

        $response = $this->actingAs($this->cdIg)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('L2 IG Alpha');
        $response->assertDontSee('L2 GFC Beta');
    }
}
