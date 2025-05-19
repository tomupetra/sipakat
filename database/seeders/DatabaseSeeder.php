<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin1234'),
        ]);

        User::create([
            'name' => 'Tomu',
            'email' => 'tomu@tomu.com',
            'password' => Hash::make('tomu1234'),
            'id_tugas' => '1',
        ]);

        User::create([
            'name' => 'Joe',
            'email' => 'joe@user.com',
            'password' => Hash::make('joe12345'),
            'id_tugas' => '2',
        ]);

        $this->call([
            RuanganSeeder::class,
        ]);
        $this->call([
            UserSeeder::class,
        ]);
        $this->call([
            SongLeaderSeeder::class,
        ]);
        $this->call([
            AvailabilitySeeder::class,
        ]);
    }
}
