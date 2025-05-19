<?php

namespace Database\Factories;

use App\Models\JadwalPelayanan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JadwalPelayananFactory extends Factory
{
    protected $model = JadwalPelayanan::class;

    public function definition()
    {
        return [
            'date' => now()->addDays(7)->format('Y-m-d'),
            'jadwal' => '07:00',
            'id_pemusik' => User::factory()->pemusik(),
            'id_sl1' => User::factory()->songLeader(),
            'id_sl2' => User::factory()->songLeader(),
        ];
    }
}
