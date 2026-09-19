<?php

namespace Tests\Feature;

use App\Models\AuditSesi;
use App\Models\Elemen;
use App\Models\Kriteria;
use App\Models\Perusahaan;
use App\Models\SubElemen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $auditorSmkp;
    protected User $auditorPerusahaan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role'      => 'admin',
            'username'  => 'admin_tester',
            'name'      => 'Admin Tester',
            'is_active' => true,
        ]);

        $this->auditorSmkp = User::factory()->create([
            'role'      => 'auditor_smkp',
            'username'  => 'auditor_smkp_tester',
            'name'      => 'Auditor SMKP Tester',
            'is_active' => true,
        ]);

        $this->auditorPerusahaan = User::factory()->create([
            'role'      => 'auditor',
            'username'  => 'auditor_perusahaan_tester',
            'name'      => 'Auditor Perusahaan Tester',
            'area'      => 'Area Tambang Barat',
            'is_active' => true,
        ]);
    }

    /**
     * Test 1: Admin can access dashboard, penilaian & monitoring, and master data.
     */
    public function test_admin_has_full_access()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get(route('admin.audit-sesi.index'))
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get(route('admin.rekap-audit.index'))
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get(route('admin.pica.index'))
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get(route('admin.users.index'))
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get(route('admin.elemens.index'))
            ->assertStatus(200);
    }

    /**
     * Test 2: Auditor SMKP can access dashboard and penilaian & monitoring.
     */
    public function test_auditor_smkp_can_access_dashboard_and_penilaian()
    {
        $this->actingAs($this->auditorSmkp)
            ->get(route('admin.dashboard'))
            ->assertStatus(200);

        $this->actingAs($this->auditorSmkp)
            ->get(route('admin.audit-sesi.index'))
            ->assertStatus(200);

        $this->actingAs($this->auditorSmkp)
            ->get(route('admin.rekap-audit.index'))
            ->assertStatus(200);

        $this->actingAs($this->auditorSmkp)
            ->get(route('admin.pica.index'))
            ->assertStatus(200);
    }

    /**
     * Test 3: Auditor SMKP CANNOT access Master Data or Kelola Users (403 Forbidden).
     */
    public function test_auditor_smkp_is_forbidden_from_master_data_and_users()
    {
        $this->actingAs($this->auditorSmkp)
            ->get(route('admin.users.index'))
            ->assertStatus(403);

        $this->actingAs($this->auditorSmkp)
            ->get(route('admin.elemens.index'))
            ->assertStatus(403);

        $this->actingAs($this->auditorSmkp)
            ->get(route('admin.sub-elemens.index'))
            ->assertStatus(403);

        $this->actingAs($this->auditorSmkp)
            ->get(route('admin.kriterias.index'))
            ->assertStatus(403);

        $this->actingAs($this->auditorSmkp)
            ->get(route('admin.perusahaans.index'))
            ->assertStatus(403);

        $this->actingAs($this->auditorSmkp)
            ->get(route('admin.audit-logs.index'))
            ->assertStatus(403);

        $this->actingAs($this->auditorSmkp)
            ->get(route('admin.restore-points.index'))
            ->assertStatus(403);
    }

    /**
     * Test 4: Role helper methods and labels.
     */
    public function test_role_labels_and_helpers()
    {
        $this->assertTrue($this->admin->isAdmin());
        $this->assertFalse($this->admin->isAuditorSmkp());
        $this->assertFalse($this->admin->isAuditor());
        $this->assertEquals('Administrator', $this->admin->role_label);

        $this->assertFalse($this->auditorSmkp->isAdmin());
        $this->assertTrue($this->auditorSmkp->isAuditorSmkp());
        $this->assertFalse($this->auditorSmkp->isAuditor());
        $this->assertEquals('Auditor SMKP', $this->auditorSmkp->role_label);

        $this->assertFalse($this->auditorPerusahaan->isAdmin());
        $this->assertFalse($this->auditorPerusahaan->isAuditorSmkp());
        $this->assertTrue($this->auditorPerusahaan->isAuditor());
        $this->assertEquals('Auditor Perusahaan', $this->auditorPerusahaan->role_label);
    }
}
