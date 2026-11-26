<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AuditLog;
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

class DeliberationPvTest extends TestCase
{
    use RefreshDatabase;

    private User $da;
    private User $cdIg;
    private User $cdFc;
    private AcademicYear $year;
    private Program $ig;
    private Program $fc;
    private Level $l1;
    private Level $l2;
    private Level $l3;
    private Semester $s1;
    private Semester $s3;
    private Semester $s5;

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

        $this->l1 = Level::where('code', 'L1')->firstOrFail();
        $this->l2 = Level::where('code', 'L2')->firstOrFail();
        $this->l3 = Level::where('code', 'L3')->firstOrFail();

        $this->s1 = Semester::where('code', 'S1')->firstOrFail();
        $this->s3 = Semester::where('code', 'S3')->firstOrFail();
        $this->s5 = Semester::where('code', 'S5')->firstOrFail();

        $this->da = User::where('role', 'da')->firstOrFail();
        $this->cdIg = User::where('role', 'cd')->where('program_id', $this->ig->id)->firstOrFail();
        $this->cdFc = User::where('role', 'cd')->where('program_id', $this->fc->id)->firstOrFail();
    }

    private function createPv(Program $program, Level $level, Semester $semester, string $className, ?string $specialty = null, string $status = DeliberationPv::STATUS_SOUMIS, ?User $creator = null): DeliberationPv
    {
        $creator = $creator ?? $this->cdIg;
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
        $stored = $file->store('private/pvs', 'local');

        return DeliberationPv::create([
            'academic_year_id' => $this->year->id,
            'program_id' => $program->id,
            'level_id' => $level->id,
            'semester_id' => $semester->id,
            'class_name' => $className,
            'specialty' => $specialty,
            'deliberation_date' => '2026-07-01',
            'file_path' => $stored,
            'file_name' => 'test.pdf',
            'file_type' => 'application/pdf',
            'file_size' => 100,
            'status' => $status,
            'created_by' => $creator->id,
        ]);
    }

    public function test_guest_is_redirected_from_pvs(): void
    {
        $response = $this->get('/pvs');
        $response->assertRedirect('/login');
    }

    public function test_da_can_see_all_pvs_on_index(): void
    {
        $this->createPv($this->ig, $this->l2, $this->s3, 'IG 2A');
        $this->createPv($this->fc, $this->l1, $this->s1, 'GFC 1A');

        $response = $this->actingAs($this->da)->get('/pvs');

        $response->assertOk();
        $response->assertSee('IG 2A');
        $response->assertSee('GFC 1A');
    }

    public function test_cd_only_sees_pvs_for_their_program_on_index(): void
    {
        $this->createPv($this->ig, $this->l2, $this->s3, 'IG 2A');
        $this->createPv($this->fc, $this->l1, $this->s1, 'GFC 1A');

        $response = $this->actingAs($this->cdIg)->get('/pvs');

        $response->assertOk();
        $response->assertSee('IG 2A');
        $response->assertDontSee('GFC 1A');
    }

    public function test_create_pv_screen_can_be_rendered(): void
    {
        $response = $this->actingAs($this->cdIg)->get('/pvs/create');
        $response->assertOk();
        $response->assertSee('Ajouter des procès-verbaux');
    }

    public function test_cd_can_submit_valid_pvs(): void
    {
        $file1 = UploadedFile::fake()->create('pv1.pdf', 200, 'application/pdf');
        $file2 = UploadedFile::fake()->create('pv2.pdf', 250, 'application/pdf');

        $response = $this->actingAs($this->cdIg)->post('/pvs', [
            'academic_year_id' => $this->year->id,
            'level_id' => $this->l2->id,
            'semester_id' => $this->s3->id,
            'deliberation_date' => '2026-07-15',
            'items' => [
                ['class_name' => 'L2 IG Promo A', 'file' => $file1],
                ['class_name' => 'L2 IG Promo B', 'file' => $file2],
            ],
        ]);

        $response->assertRedirect(route('pvs.index'));
        $this->assertDatabaseHas('deliberation_pvs', [
            'class_name' => 'L2 IG Promo A',
            'program_id' => $this->ig->id,
            'status' => DeliberationPv::STATUS_SOUMIS,
        ]);
        $this->assertDatabaseHas('deliberation_pvs', [
            'class_name' => 'L2 IG Promo B',
            'program_id' => $this->ig->id,
            'status' => DeliberationPv::STATUS_SOUMIS,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => AuditLog::ACTION_CREATED,
            'user_id' => $this->cdIg->id,
        ]);
    }

    public function test_specialty_accepted_for_ig_l3(): void
    {
        $file = UploadedFile::fake()->create('pv_ari.pdf', 200, 'application/pdf');

        $response = $this->actingAs($this->cdIg)->post('/pvs', [
            'academic_year_id' => $this->year->id,
            'level_id' => $this->l3->id,
            'semester_id' => $this->s5->id,
            'specialty' => 'ARI',
            'deliberation_date' => '2026-07-15',
            'items' => [
                ['class_name' => 'L3 IG-ARI A', 'file' => $file],
            ],
        ]);

        $response->assertRedirect(route('pvs.index'));
        $this->assertDatabaseHas('deliberation_pvs', [
            'class_name' => 'L3 IG-ARI A',
            'specialty' => 'ARI',
        ]);
    }

    public function test_specialty_rejected_for_non_ig_program(): void
    {
        $file = UploadedFile::fake()->create('pv.pdf', 200, 'application/pdf');

        $response = $this->actingAs($this->cdFc)->post('/pvs', [
            'academic_year_id' => $this->year->id,
            'level_id' => $this->l3->id,
            'semester_id' => $this->s5->id,
            'specialty' => 'ARI',
            'deliberation_date' => '2026-07-15',
            'items' => [
                ['class_name' => 'L3 GFC A', 'file' => $file],
            ],
        ]);

        $response->assertSessionHasErrors(['specialty']);
    }

    public function test_specialty_rejected_for_non_l3_level(): void
    {
        $file = UploadedFile::fake()->create('pv.pdf', 200, 'application/pdf');

        $response = $this->actingAs($this->cdIg)->post('/pvs', [
            'academic_year_id' => $this->year->id,
            'level_id' => $this->l1->id,
            'semester_id' => $this->s1->id,
            'specialty' => 'ARI',
            'deliberation_date' => '2026-07-15',
            'items' => [
                ['class_name' => 'L1 IG A', 'file' => $file],
            ],
        ]);

        $response->assertSessionHasErrors(['specialty']);
    }

    public function test_incoherent_level_semester_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('pv.pdf', 200, 'application/pdf');

        $response = $this->actingAs($this->cdIg)->post('/pvs', [
            'academic_year_id' => $this->year->id,
            'level_id' => $this->l1->id,
            'semester_id' => $this->s5->id, // L1 should have S1 or S2, not S5
            'deliberation_date' => '2026-07-15',
            'items' => [
                ['class_name' => 'L1 IG A', 'file' => $file],
            ],
        ]);

        $response->assertSessionHasErrors(['semester_id']);
    }

    public function test_duplicate_class_name_in_same_batch_rejected(): void
    {
        $file1 = UploadedFile::fake()->create('pv1.pdf', 200, 'application/pdf');
        $file2 = UploadedFile::fake()->create('pv2.pdf', 200, 'application/pdf');

        $response = $this->actingAs($this->cdIg)->post('/pvs', [
            'academic_year_id' => $this->year->id,
            'level_id' => $this->l2->id,
            'semester_id' => $this->s3->id,
            'deliberation_date' => '2026-07-15',
            'items' => [
                ['class_name' => 'L2 IG Doublon', 'file' => $file1],
                ['class_name' => 'L2 IG Doublon', 'file' => $file2],
            ],
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_duplicate_class_name_existing_in_db_rejected(): void
    {
        $this->createPv($this->ig, $this->l2, $this->s3, 'L2 IG Existant');

        $file = UploadedFile::fake()->create('pv.pdf', 200, 'application/pdf');

        $response = $this->actingAs($this->cdIg)->post('/pvs', [
            'academic_year_id' => $this->year->id,
            'level_id' => $this->l2->id,
            'semester_id' => $this->s3->id,
            'deliberation_date' => '2026-07-15',
            'items' => [
                ['class_name' => 'L2 IG Existant', 'file' => $file],
            ],
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_cd_can_view_pv_of_their_program(): void
    {
        $pv = $this->createPv($this->ig, $this->l2, $this->s3, 'IG 2A');

        $response = $this->actingAs($this->cdIg)->get("/pvs/{$pv->id}");
        $response->assertOk();
        $response->assertSee('IG 2A');
    }

    public function test_cd_cannot_view_pv_of_another_program(): void
    {
        $pv = $this->createPv($this->fc, $this->l1, $this->s1, 'GFC 1A');

        $response = $this->actingAs($this->cdIg)->get("/pvs/{$pv->id}");
        $response->assertForbidden();
    }

    public function test_da_can_archive_submitted_pv(): void
    {
        $pv = $this->createPv($this->ig, $this->l2, $this->s3, 'IG 2A', null, DeliberationPv::STATUS_SOUMIS);

        $response = $this->actingAs($this->da)->post("/pvs/{$pv->id}/archive");
        $response->assertRedirect();

        $pv->refresh();
        $this->assertSame(DeliberationPv::STATUS_ARCHIVE, $pv->status);
        $this->assertNotNull($pv->archived_at);

        $this->assertDatabaseHas('audit_logs', [
            'action' => AuditLog::ACTION_ARCHIVED,
            'user_id' => $this->da->id,
        ]);
    }

    public function test_cd_cannot_archive_pv(): void
    {
        $pv = $this->createPv($this->ig, $this->l2, $this->s3, 'IG 2A', null, DeliberationPv::STATUS_SOUMIS);

        $response = $this->actingAs($this->cdIg)->post("/pvs/{$pv->id}/archive");
        $response->assertForbidden();

        $pv->refresh();
        $this->assertSame(DeliberationPv::STATUS_SOUMIS, $pv->status);
    }

    public function test_cannot_update_archived_pv(): void
    {
        $pv = $this->createPv($this->ig, $this->l2, $this->s3, 'IG 2A', null, DeliberationPv::STATUS_ARCHIVE);

        $response = $this->actingAs($this->da)->get("/pvs/{$pv->id}/edit");
        $response->assertForbidden();

        $response = $this->actingAs($this->da)->put("/pvs/{$pv->id}", [
            'academic_year_id' => $this->year->id,
            'program_id' => $this->ig->id,
            'level_id' => $this->l2->id,
            'semester_id' => $this->s3->id,
            'class_name' => 'IG 2A Modif',
            'deliberation_date' => '2026-07-16',
        ]);
        $response->assertForbidden();
    }

    public function test_authorized_user_can_view_file(): void
    {
        $pv = $this->createPv($this->ig, $this->l2, $this->s3, 'IG 2A');

        $response = $this->actingAs($this->cdIg)->get("/pvs/{$pv->id}/file");
        $response->assertOk();
        $this->assertDatabaseHas('audit_logs', [
            'action' => AuditLog::ACTION_VIEWED,
            'user_id' => $this->cdIg->id,
        ]);
    }

    public function test_unauthorized_user_cannot_view_file(): void
    {
        $pv = $this->createPv($this->fc, $this->l1, $this->s1, 'GFC 1A');

        $response = $this->actingAs($this->cdIg)->get("/pvs/{$pv->id}/file");
        $response->assertForbidden();
    }
}
