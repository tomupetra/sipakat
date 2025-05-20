<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\JadwalPelayanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanPelayananTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);
        return $admin;
    }

    public function test_admin_mengakses_laporan_pelayanan()
    {
        $admin = $this->actingAsAdmin();

        // Buat data dummy jadwal pelayanan
        JadwalPelayanan::factory()->create([
            'date' => now()->format('Y-m-d'),
            'jadwal' => '07:00',
            'id_pemusik' => User::factory()->pemusik()->create()->id,
            'id_sl1' => User::factory()->songLeader()->create()->id,
            'id_sl2' => User::factory()->songLeader()->create()->id,
            'confirmation_deadline' => now()->addDay(3),
        ]);

        $response = $this->get(route('laporan.pelayanan'));
        $response->assertStatus(200);
        $response->assertSeeText('Riwayat Jadwal Pelayanan');
    }

    public function test_admin_mengunduh_laporan_pelayanan_pdf()
    {
        $admin = $this->actingAsAdmin();

        // Buat data dummy jadwal pelayanan
        JadwalPelayanan::factory()->create([
            'date' => now()->format('Y-m-d'),
            'jadwal' => '10:00',
            'id_pemusik' => User::factory()->create()->id,
            'id_sl1' => User::factory()->create()->id,
            'id_sl2' => User::factory()->create()->id,
            'confirmation_deadline' => now()->addDay(3),
        ]);

        $response = $this->get(route('laporan.exportPDF'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
