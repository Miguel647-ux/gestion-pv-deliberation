<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Program;
use App\Models\User;
use Database\Seeders\AcademicYearSeeder;
use Database\Seeders\LevelSeeder;
use Database\Seeders\ProgramSeeder;
use Database\Seeders\SemesterSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $da;
    private User $cd;

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

        $this->da = User::where('role', 'da')->firstOrFail();
        $this->cd = User::where('role', 'cd')->firstOrFail();
    }

    public function test_guest_is_redirected_from_admin_pages(): void
    {
        $this->get('/admin/years')->assertRedirect('/login');
        $this->get('/admin/users')->assertRedirect('/login');
        $this->get('/admin/logs')->assertRedirect('/login');
    }

    public function test_cd_cannot_access_admin_years(): void
    {
        $response = $this->actingAs($this->cd)->get('/admin/years');
        $response->assertForbidden();
    }

    public function test_cd_cannot_access_admin_users(): void
    {
        $response = $this->actingAs($this->cd)->get('/admin/users');
        $response->assertForbidden();
    }

    public function test_cd_cannot_access_admin_logs(): void
    {
        $response = $this->actingAs($this->cd)->get('/admin/logs');
        $response->assertForbidden();
    }

    public function test_da_can_access_admin_years(): void
    {
        $response = $this->actingAs($this->da)->get('/admin/years');
        $response->assertOk();
        $response->assertSee('Années académiques');
    }

    public function test_da_can_create_academic_year(): void
    {
        $response = $this->actingAs($this->da)->post('/admin/years', [
            'label' => '2026-2027',
            'starts_on' => '2026-10-01',
            'ends_on' => '2027-07-31',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.years.index'));
        $this->assertDatabaseHas('academic_years', [
            'label' => '2026-2027',
            'is_active' => true,
        ]);

        // Previous year should be set to inactive
        $this->assertDatabaseHas('academic_years', [
            'label' => '2025-2026',
            'is_active' => false,
        ]);
    }

    public function test_da_can_update_academic_year(): void
    {
        $year = AcademicYear::where('label', '2025-2026')->firstOrFail();

        $response = $this->actingAs($this->da)->put("/admin/years/{$year->id}", [
            'label' => '2026-2027',
            'starts_on' => '2025-10-01',
            'ends_on' => '2026-07-31',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.years.index'));
        $this->assertDatabaseHas('academic_years', [
            'id' => $year->id,
            'label' => '2026-2027',
        ]);
    }

    public function test_da_can_access_admin_users(): void
    {
        $response = $this->actingAs($this->da)->get('/admin/users');
        $response->assertOk();
        $response->assertSee('Comptes utilisateurs');
        $response->assertSee('da@eneam.bj');
    }

    public function test_da_can_access_admin_logs(): void
    {
        $response = $this->actingAs($this->da)->get('/admin/logs');
        $response->assertOk();
        $response->assertSee('Journal', false);
        $response->assertSee('audit', false);
    }
}
