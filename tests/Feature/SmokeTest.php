<?php

namespace Tests\Feature;

use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role'      => 'admin',
            'username'  => 'smoke_admin',
            'password'  => bcrypt('password123'),
            'is_active' => true,
        ]);
    }

    /**
     * Smoke Test 1: Halaman login dapat diakses oleh guest.
     */
    public function test_guest_can_render_login_page(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Smoke Test 2: Autentikasi user berhasil dan redirect ke dashboard admin.
     */
    public function test_user_can_authenticate_and_redirect_to_dashboard(): void
    {
        $response = $this->post('/login', [
            'username' => 'smoke_admin',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);
    }

    /**
     * Smoke Test 3: Admin yang login dapat mengakses halaman utama / dashboard.
     */
    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    /**
     * Smoke Test 4: Alur CRUD Master Perusahaan berjalan dengan benar.
     */
    public function test_admin_can_perform_crud_on_perusahaan(): void
    {
        $this->actingAs($this->admin);

        // 1. Create (Store)
        $storeResponse = $this->post(route('admin.perusahaans.store'), [
            'nama_perusahaan' => 'PT Tambang Maju Smoke',
            'kode_perusahaan' => 'PTMS',
            'kategori'        => 'Pemegang IUP',
        ]);

        $storeResponse->assertRedirect(route('admin.perusahaans.index'));
        $this->assertDatabaseHas('perusahaans', [
            'nama_perusahaan' => 'PT Tambang Maju Smoke',
            'kode_perusahaan' => 'PTMS',
        ]);

        $perusahaan = Perusahaan::where('nama_perusahaan', 'PT Tambang Maju Smoke')->first();
        $this->assertNotNull($perusahaan);

        // 2. Read (Index)
        $indexResponse = $this->get(route('admin.perusahaans.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('PT Tambang Maju Smoke');

        // 3. Update
        $updateResponse = $this->put(route('admin.perusahaans.update', $perusahaan->id), [
            'nama_perusahaan' => 'PT Tambang Maju Smoke Updated',
            'kode_perusahaan' => 'PTMS-REV',
            'kategori'        => 'Kontraktor',
        ]);

        $updateResponse->assertRedirect(route('admin.perusahaans.index'));
        $this->assertDatabaseHas('perusahaans', [
            'id'              => $perusahaan->id,
            'nama_perusahaan' => 'PT Tambang Maju Smoke Updated',
            'kategori'        => 'Kontraktor',
        ]);

        // 4. Delete (Soft Delete)
        $deleteResponse = $this->delete(route('admin.perusahaans.destroy', $perusahaan->id));
        $deleteResponse->assertRedirect(route('admin.perusahaans.index'));
        $this->assertSoftDeleted('perusahaans', [
            'id' => $perusahaan->id,
        ]);
    }
}
