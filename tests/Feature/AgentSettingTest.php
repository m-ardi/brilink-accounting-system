<?php

namespace Tests\Feature;

use App\Models\AgentSetting;
use App\Models\Modal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgentSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed initial agent settings and modal balance
        Modal::setSaldo(5000000);
        AgentSetting::updateOrCreate(['key' => 'nama_agen'], ['value' => 'Agen BRILink Rambo', 'description' => 'Nama']);
        AgentSetting::updateOrCreate(['key' => 'alamat_agen'], ['value' => 'Dr. Ratulangi', 'description' => 'Alamat']);
        AgentSetting::updateOrCreate(['key' => 'kode_agen'], ['value' => 'RAMBO-001', 'description' => 'Kode']);
    }

    public function test_owner_can_access_settings_page(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this
            ->actingAs($owner)
            ->get('/settings');

        $response->assertOk();
        $response->assertSee('Agen BRILink Rambo');
        $response->assertSee('Dr. Ratulangi');
        $response->assertSee('5.000.000');
    }

    public function test_staff_cannot_access_settings_page(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this
            ->actingAs($staff)
            ->get('/settings');

        $response->assertStatus(403);
    }

    public function test_owner_can_update_agent_identity_settings(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this
            ->actingAs($owner)
            ->post('/settings', [
                'nama_agen' => 'Agen BRILink Baru',
                'alamat_agen' => 'Alamat Baru',
                'kode_agen' => 'BARU-002',
            ]);

        $response->assertRedirect('/settings');
        $response->assertSessionHasNoErrors();

        $this->assertEquals('Agen BRILink Baru', AgentSetting::get('nama_agen'));
        $this->assertEquals('Alamat Baru', AgentSetting::get('alamat_agen'));
        $this->assertEquals('BARU-002', AgentSetting::get('kode_agen'));
    }

    public function test_owner_can_add_modal_balance(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this
            ->actingAs($owner)
            ->post('/settings', [
                'nama_agen' => 'Agen BRILink Rambo',
                'alamat_agen' => 'Dr. Ratulangi',
                'kode_agen' => 'RAMBO-001',
                'tipe_penyesuaian' => 'tambah',
                'nominal_penyesuaian' => '2500000',
                'keterangan_penyesuaian' => 'Suntik Modal Tambahan',
            ]);

        $response->assertRedirect('/settings');
        $response->assertSessionHasNoErrors();

        $this->assertEquals(7500000.0, Modal::getSaldo());

        // Check if activity log was created
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $owner->id,
            'action' => 'updated',
        ]);
    }

    public function test_owner_can_withdraw_modal_balance(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this
            ->actingAs($owner)
            ->post('/settings', [
                'nama_agen' => 'Agen BRILink Rambo',
                'alamat_agen' => 'Dr. Ratulangi',
                'kode_agen' => 'RAMBO-001',
                'tipe_penyesuaian' => 'kurang',
                'nominal_penyesuaian' => '1000000',
                'keterangan_penyesuaian' => 'Tarik Tunai Modal',
            ]);

        $response->assertRedirect('/settings');
        $response->assertSessionHasNoErrors();

        $this->assertEquals(4000000.0, Modal::getSaldo());
    }
}
