<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ruangan;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            ['name' => 'Ruang Gereja', 'color' => '#041e42'],
            ['name' => 'Konsistori', 'color' => '#b2cae4'],
            ['name' => 'Kantor Pendeta', 'color' => '#bab49e'],
            ['name' => 'Gedung Serba Guna', 'color' => '#b26801'],
            ['name' => 'Aula', 'color' => '#008080'],
            ['name' => 'Kantor Tata Usaha', 'color' => '#d4af37'],
            ['name' => 'Ruang Sekolah Minggu', 'color' => '#ff7f50'],
            ['name' => 'Ruang Remaja dan Naposo', 'color' => '#007f4e'],
        ];

        foreach ($rooms as $room) {
            Ruangan::create($room);
        }
    }
}
