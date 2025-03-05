<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker; // Import library faker

class SongLeaderSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID'); // Inisialisasi faker dengan locale Indonesia
        $jumlahData = 15; // Jumlah data Song Leader yang ingin dimasukkan

        for ($i = 0; $i < $jumlahData; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'), // Ganti dengan password default atau generate acak
                'id_tugas' => 2,
                // ... kolom lain yang perlu diisi (jika ada)
            ]);
        }
    }
}