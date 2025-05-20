<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Ruangan;
use App\Models\PinjamRuangan;

class PinjamRuanganTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);
        return $admin;
    }

    protected function actingAsUser()
    {
        $user = User::factory()->pemusik()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_user_ajukan_pinjam_ruangan()
    {
        $user = $this->actingAsUser();
        $ruangan = Ruangan::factory()->create();

        $response = $this->post('/bookings', [
            'room_id' => $ruangan->id,
            'kegiatan' => 'Rapat Divisi',
            'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('jadwal-ruangan'));
        $this->assertDatabaseHas('pinjamruangan', [
            'user_id' => $user->id,
            'room_id' => $ruangan->id,
            'kegiatan' => 'Rapat Divisi',
            'status' => 'Diajukan',
        ]);
    }

    public function test_admin_konfirmasi_peminjaman_ruangan()
    {
        $admin = $this->actingAsAdmin();
        $user = User::factory()->create(['role' => 'user']);
        $ruangan = Ruangan::factory()->create();

        $booking = PinjamRuangan::create([
            'room_id' => $ruangan->id,
            'user_id' => $user->id,
            'kegiatan' => 'Rapat Divisi',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDays(2),
            'status' => 'Diajukan',
        ]);

        $response = $this->post("/admin/pinjam-ruangan/{$booking->id}/status", [
            'status' => 'Disetujui',
        ]);

        $response->assertRedirect(route('admin.bookings.index'));
        $this->assertDatabaseHas('pinjamruangan', [
            'id' => $booking->id,
            'status' => 'Disetujui',
        ]);
    }

    public function test_admin_menolak_peminjaman_ruangan()
    {
        $admin = $this->actingAsAdmin();
        $user = User::factory()->create(['role' => 'user']);
        $ruangan = Ruangan::factory()->create();

        $booking = PinjamRuangan::create([
            'room_id' => $ruangan->id,
            'user_id' => $user->id,
            'kegiatan' => 'Rapat Divisi',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDays(2),
            'status' => 'Diajukan',
        ]);

        $response = $this->post("/admin/pinjam-ruangan/{$booking->id}/status", [
            'status' => 'Ditolak',
        ]);

        $response->assertRedirect(route('admin.bookings.index'));
        $this->assertDatabaseHas('pinjamruangan', [
            'id' => $booking->id,
            'status' => 'Ditolak',
        ]);
    }
}
