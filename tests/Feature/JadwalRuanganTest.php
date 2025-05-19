<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ruangan;
use App\Models\Jadwal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class JadwalRuanganTest extends TestCase
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

    public function test_admin_can_create_schedule()
    {
        $this->actingAsAdmin();

        $response = $this->postJson(route('jadwal.create'), [
            'title' => 'Tes Rapat',
            'start' => now()->addDay()->format('Y-m-d\TH:i'),
            'end' => now()->addDays(1)->addHours(2)->format('Y-m-d\TH:i'),
            'description' => 'Rapat evaluasi bulanan',
            'color' => '#123456',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Jadwal berhasil ditambahkan.']);

        $this->assertDatabaseHas('jadwal', ['title' => 'Tes Rapat']);
    }

    public function test_admin_can_update_schedule()
    {
        $this->actingAsAdmin();

        $jadwal = Jadwal::create([
            'title' => 'Tes Awal',
            'start' => now()->format('Y-m-d H:i:s'),
            'end' => now()->addHour()->format('Y-m-d H:i:s'),
            'description' => 'Desc',
            'color' => '#111111',
        ]);

        $response = $this->putJson('/admin/ruangan/update/' . $jadwal->id, [
            'title' => 'Tes Update',
            'start' => now()->addDays(1)->format('Y-m-d\TH:i'),
            'end' => now()->addDays(1)->addHours(2)->format('Y-m-d\TH:i'),
            'description' => 'Updated description',
            'color' => '#654321',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Jadwal berhasil diperbarui.']);

        $this->assertDatabaseHas('jadwal', ['title' => 'Tes Update']);
    }

    public function test_admin_can_delete_schedule()
    {
        $this->actingAsAdmin();

        $jadwal = Jadwal::create([
            'title' => 'To Be Deleted',
            'start' => now()->format('Y-m-d H:i:s'),
            'end' => now()->addHour()->format('Y-m-d H:i:s'),
            'description' => 'Hapus saya',
            'color' => '#000000',
        ]);

        $response = $this->deleteJson('/admin/ruangan/delete/' . $jadwal->id);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Event deleted successfully.']);

        $this->assertDatabaseMissing('jadwal', ['id' => $jadwal->id]);
    }

    public function test_user_can_view_schedule_page()
    {
        $this->actingAsUser();

        Ruangan::create([
            'name' => 'Ruang Serbaguna',
            'color' => '#ff0000',
        ]);

        Jadwal::create([
            'title' => 'Tes Seminar',
            'start' => now()->format('Y-m-d H:i:s'),
            'end' => now()->addHour()->format('Y-m-d H:i:s'),
            'description' => 'Seminar tahunan',
            'color' => '#ff0000',
        ]);

        $response = $this->get('/user/jadwal-ruangan');

        $response->assertStatus(200);
        $response->assertSeeText('Tes Seminar');
        $response->assertSeeText('Ruang Serbaguna');
    }
}
