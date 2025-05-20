<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\JadwalPelayanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Carbon\Carbon;

class JadwalPelayananUserTest extends TestCase
{
    use RefreshDatabase;

    protected function createUserWithRole($name, $id_tugas)
    {
        return User::create([
            'name' => $name . uniqid(),
            'email' => $name . uniqid() . '@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'id_tugas' => $id_tugas,
        ]);
    }

    public function test_user_can_see_their_schedules()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->pemusik()->create();
        $this->actingAs($user);

        JadwalPelayanan::create([
            'date' => now()->format('Y-m-d'),
            'jadwal' => '07:00',
            'id_pemusik' => $user->id,
            'id_sl1' => $this->createUserWithRole('sl1', 2)->id,
            'id_sl2' => $this->createUserWithRole('sl2', 2)->id,
            'confirmation_deadline' => now()->addDay(3),
        ]);

        $response = $this->get('/user/jadwal-pelayanan');
        $response->assertStatus(200);
        $response->assertSeeText('Jadwal Pelayanan Anda');
    }

    public function test_user_can_confirm_schedule()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->songLeader()->create();
        $this->actingAs($user);

        $jadwal = JadwalPelayanan::create([
            'date' => now()->format('Y-m-d'),
            'jadwal' => '10:00',
            'id_pemusik' => $this->createUserWithRole('pemusik', 1)->id,
            'id_sl1' => $user->id,
            'id_sl2' => $this->createUserWithRole('sl2', 2)->id,
            'confirmation_deadline' => Carbon::now()->addDay(),
            'status_sl1' => 0,
        ]);

        $response = $this->post("/jadwal/{$jadwal->id}/confirm");
        $response->assertRedirect();
        $this->assertDatabaseHas('jadwal_pelayanan', [
            'id' => $jadwal->id,
            'status_sl1' => 1,
        ]);
    }

    public function test_user_can_reject_schedule()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->pemusik()->create();
        $this->actingAs($user);

        $jadwal = JadwalPelayanan::create([
            'date' => now()->format('Y-m-d'),
            'jadwal' => '18:00',
            'id_pemusik' => $user->id,
            'id_sl1' => $this->createUserWithRole('sl1', 2)->id,
            'id_sl2' => $this->createUserWithRole('sl2', 2)->id,
            'confirmation_deadline' => Carbon::now()->addDay(),
            'status_pemusik' => 0,
        ]);

        $response = $this->post("/jadwal/{$jadwal->id}/reject");
        $response->assertRedirect();
        $this->assertDatabaseHas('jadwal_pelayanan', [
            'id' => $jadwal->id,
            'status_pemusik' => 2,
        ]);
    }
}
