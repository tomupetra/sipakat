<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Warta;

class WartaTest extends TestCase
{
    public function test_user_can_access_landing_page()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('landingpage.landingpage');
    }

    public function test_user_sees_message_when_warta_jemaat_not_available()
    {
        Warta::truncate(); // Hapus data jika perlu
        $response = $this->get('/');
        $response->assertSee('Warta Jemaat belum tersedia');
    }

    public function test_user_can_view_warta_jemaat_link_when_file_exists()
    {
        Warta::firstOrCreate(['file_name' => 'test-bulletin.pdf']);

        $response = $this->get('/');
        $response->assertSee('Lihat Warta Jemaat');
        $response->assertSee('test-bulletin.pdf');
    }

    public function test_user_can_download_warta_file()
    {
        $filename = 'bulletin.pdf';
        $folderPath = storage_path('app/public/warta');
        $filepath = "{$folderPath}/{$filename}";

        // Buat folder jika belum ada
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        // Buat file dummy
        if (!file_exists($filepath)) {
            file_put_contents($filepath, '%PDF-1.4 dummy content');
        }

        Warta::firstOrCreate(['file_name' => $filename]);

        $response = $this->get("/warta/{$filename}");
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
