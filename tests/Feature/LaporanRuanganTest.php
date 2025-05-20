<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ruangan;
use App\Models\PinjamRuangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanRuanganTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);
        return $admin;
    }

    public function test_admin_can_access_laporan_ruangan()
    {
        $admin = $this->actingAsAdmin();
        $ruangan = Ruangan::factory()->create();
        $user = User::factory()->create();

        // Buat data pinjam ruangan
        PinjamRuangan::create([
            'room_id' => $ruangan->id,
            'user_id' => $user->id,
            'kegiatan' => 'Rapat Koordinasi',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDays(2),
            'status' => 'Disetujui',
        ]);

        $response = $this->get(route('admin.laporan-ruangan'));
        $response->assertStatus(200);
        $response->assertSeeText('Laporan Peminjaman Ruangan');
        $response->assertSeeText('Rapat Koordinasi');
        $response->assertSeeText($ruangan->name);
    }

    public function test_admin_can_export_laporan_ruangan_pdf()
    {
        $admin = $this->actingAsAdmin();
        $ruangan = Ruangan::factory()->create();
        $user = User::factory()->create();

        // Buat data pinjam ruangan
        PinjamRuangan::create([
            'room_id' => $ruangan->id,
            'user_id' => $user->id,
            'kegiatan' => 'Rapat Koordinasi',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDays(2),
            'status' => 'Disetujui',
        ]);

        $response = $this->get(route('admin.laporan-ruangan.export-pdf'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
