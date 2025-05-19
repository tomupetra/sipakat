<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AdminKelolaAkunTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'id_tugas' => 1
        ]);

        $this->actingAs($admin);
        return $admin;
    }

    public function test_admin_can_view_user_list()
    {
        $this->actingAsAdmin();

        $response = $this->get('/admin/kelolaakun');
        $response->assertStatus(200);
        $response->assertSee('Kelola Akun');
    }

    public function test_admin_can_create_new_user()
    {
        $this->actingAsAdmin();

        $response = $this->post('/admin/add', [
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => 'securepass',
            'id_tugas' => 1
        ]);

        $response->assertRedirect('/admin/kelolaakun');
        $this->assertDatabaseHas('users', ['email' => 'user@example.com']);
    }

    public function test_admin_can_edit_existing_user()
    {
        $this->actingAsAdmin();

        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'editme@example.com',
            'password' => Hash::make('oldpass'),
            'role' => 'user',
            'id_tugas' => 2
        ]);

        $response = $this->post("/admin/edit/{$user->id}", [
            'name' => 'Updated Name',
            'email' => 'editme@example.com',
            'id_tugas' => 1,
            'password' => 'newsecurepass'
        ]);

        $response->assertRedirect('/admin/kelolaakun');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'editme@example.com',
            'id_tugas' => 1
        ]);
    }

    public function test_admin_can_delete_user()
    {
        $this->actingAsAdmin();

        $user = User::factory()->create([
            'name' => 'Delete Me',
            'email' => 'deleteme@example.com',
            'role' => 'user',
            'id_tugas' => 1
        ]);

        $response = $this->delete("/admin/delete/{$user->id}");
        $response->assertRedirect('/admin/kelolaakun');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
