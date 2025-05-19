<?php

namespace Tests\Feature;

use App\Models\Availability;
use App\Models\JadwalPelayanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);
        return $admin;
    }

    public function test_admin_can_generate_schedule()
    {
        Notification::fake();
        $this->actingAsAdmin();

        // Buat 3 pemusik dan 6 song leader dengan ketersediaan
        $pemusik = User::factory()->count(3)->pemusik()->create();
        $sl = User::factory()->count(6)->songLeader()->create();

        $pemusik->each(fn($user) => Availability::factory()->create(['user_id' => $user->id]));
        $sl->each(fn($user) => Availability::factory()->create(['user_id' => $user->id]));

        $jadwalCountBefore = JadwalPelayanan::count();

        $response = $this->get(route('admin.generate-schedule'));
        $response->assertRedirect();

        $jadwalCountAfter = JadwalPelayanan::count();
        $this->assertGreaterThan($jadwalCountBefore, $jadwalCountAfter);
    }

    public function test_admin_cannot_generate_duplicate_schedule_for_current_month()
    {
        Notification::fake();
        $this->actingAsAdmin();

        $jadwal = JadwalPelayanan::factory()->create();

        $jadwalCountBefore = JadwalPelayanan::count();

        $response = $this->get(route('admin.generate-schedule'));
        $response->assertRedirect();

        $jadwalCountAfter = JadwalPelayanan::count();
        $this->assertEquals($jadwalCountBefore, $jadwalCountAfter);
    }

    public function test_admin_can_generate_next_month_schedule()
    {
        Notification::fake();
        $this->actingAsAdmin();

        $pemusik = User::factory()->count(3)->pemusik()->create();
        $sl = User::factory()->count(6)->songLeader()->create();

        $pemusik->each(fn($user) => Availability::factory()->create(['user_id' => $user->id]));
        $sl->each(fn($user) => Availability::factory()->create(['user_id' => $user->id]));

        $jadwalCountBefore = JadwalPelayanan::count();

        $response = $this->get(route('admin.generate-next-month-schedule'));
        $response->assertRedirect();

        $jadwalCountAfter = JadwalPelayanan::count();
        $this->assertGreaterThan($jadwalCountBefore, $jadwalCountAfter);
    }

    public function test_admin_can_update_schedule()
    {
        Notification::fake();
        $this->actingAsAdmin();

        $jadwal = JadwalPelayanan::factory()->create();

        $newPemusik = User::factory()->pemusik()->create();
        $newSl1 = User::factory()->songLeader()->create();
        $newSl2 = User::factory()->songLeader()->create();

        $response = $this->put(route('admin.update-jadwal', $jadwal->id), [
            'date' => now()->addDays(10)->format('Y-m-d'),
            'jadwal' => '10:00',
            'id_pemusik' => $newPemusik->id,
            'id_sl1' => $newSl1->id,
            'id_sl2' => $newSl2->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('jadwal_pelayanan', [
            'id' => $jadwal->id,
            'id_pemusik' => $newPemusik->id,
        ]);
    }

    public function test_admin_can_delete_schedule()
    {
        Notification::fake();
        $this->actingAsAdmin();

        $jadwal = JadwalPelayanan::factory()->create();

        $response = $this->delete(route('admin.delete-jadwal', $jadwal->id));
        $response->assertRedirect();

        $this->assertDatabaseMissing('jadwal_pelayanan', ['id' => $jadwal->id]);
    }
}
