<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Berita;
use App\Models\Renungan;
use App\Models\Warta;
use App\Models\Gallery;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AdminHomepageTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin);
        return $admin;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin(); // ini memastikan semua test pakai admin login
    }

    // ---------------------
    // BERITA
    // ---------------------
    public function test_admin_can_manage_berita()
    {
        // Create
        $data = [
            'date' => now()->format('Y-m-d'),
            'title' => 'Judul Berita',
            'content' => 'Konten Berita',
        ];
        $response = $this->post('/admin/berita/tambah', $data);
        $response->assertRedirect('/admin/berita');
        $this->assertDatabaseHas('berita', ['title' => 'Judul Berita']);

        // Update
        $berita = Berita::first();
        $response = $this->post("/admin/berita/edit/{$berita->id}", [
            'title' => 'Judul Update',
            'content' => 'Isi update',
            'date' => now()->format('Y-m-d')
        ]);
        $response->assertRedirect('/admin/berita');
        $this->assertDatabaseHas('berita', ['id' => $berita->id, 'title' => 'Judul Update']);

        // Delete
        $response = $this->delete("/admin/berita/delete/{$berita->id}");
        $response->assertRedirect('/admin/berita');
        $this->assertDatabaseMissing('berita', ['id' => $berita->id]);
    }

    // ---------------------
    // RENUNGAN
    // ---------------------
    public function test_admin_can_manage_renungan()
    {
        // Create
        $data = [
            'date' => now()->format('Y-m-d'),
            'ayat_harian' => 'Filipi 4:13',
            'bacaan_pagi' => 'Mazmur 1',
            'bacaan_malam' => 'Yohanes 3',
            'lagu_ende' => 'No. 122',
            'title' => 'Renungan Hari Ini',
            'content' => 'Isi renungan',
        ];
        $response = $this->post('/admin/renungan/tambah', $data);
        $response->assertRedirect('/admin/renungan/list');
        $this->assertDatabaseHas('renungan', ['title' => 'Renungan Hari Ini']);

        // Update
        $renungan = Renungan::first();
        $response = $this->post("/admin/renungan/edit/{$renungan->id}", [
            'date' => now()->format('Y-m-d'),
            'ayat_harian' => 'Mazmur 23',
            'bacaan_pagi' => 'Yesaya 40',
            'bacaan_malam' => 'Yohanes 5',
            'lagu_ende' => 'No. 145',
            'title' => 'Renungan Update',
            'content' => 'Isi update',
        ]);
        $response->assertRedirect('/admin/renungan/list');
        $this->assertDatabaseHas('renungan', ['id' => $renungan->id, 'title' => 'Renungan Update']);

        // Delete
        $response = $this->delete("/admin/renungan/delete/{$renungan->id}");
        $response->assertRedirect('/admin/renungan/list');
        $this->assertDatabaseMissing('renungan', ['id' => $renungan->id]);
    }

    // ---------------------
    // WARTA
    // ---------------------
    public function test_admin_can_manage_warta()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('warta.pdf', 100, 'application/pdf');

        // Upload
        $response = $this->post('/admin/warta/upload-warta', [
            'warta' => $file,
            'date' => now()->format('Y-m-d'),
        ]);
        $response->assertRedirect('/admin/warta');
        $warta = Warta::first();
        $this->assertNotNull($warta);

        // Delete
        $response = $this->delete("/admin/warta/destroy/{$warta->id}");
        $response->assertRedirect('/admin/warta');
        $this->assertDatabaseMissing('warta', ['id' => $warta->id]);
    }

    // ---------------------
    // GALERI
    // ---------------------
    public function test_admin_can_manage_gallery()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('foto.jpg');

        // Upload
        $response = $this->post('/admin/galeri/upload', [
            'image' => $file
        ]);
        $response->assertRedirect();
        $gallery = Gallery::first();
        $this->assertNotNull($gallery);

        // Delete
        $response = $this->delete("/admin/galeri/delete/{$gallery->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('gallery', ['id' => $gallery->id]);
    }
}
