<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Availability; // Import Availability
use App\Models\JadwalPelayanan;
use App\Models\User; // Import User
use Illuminate\Support\Facades\Log; // Import Log facade
use App\Services\GeneticAlgorithm;

class PelayananController extends Controller
{
    public function generateSchedule(Request $request)
    {
        // 1. Validasi input (opsional, tapi disarankan)
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'services' => 'required|array', // Pastikan services adalah array. Contoh: ['Pagi', 'Sore']
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $services = $request->input('services');

        // 2. Ambil data ketersediaan dari database

        //  Pemusik (id_tugas = 1)
        $availableMusicians = Availability::whereBetween('date', [$startDate, $endDate])
            ->whereHas('user', function ($query) {
                $query->where('id_tugas', 1);
            })
            ->get()
            ->groupBy('date') // Group by date
            ->map(function ($availabilities) {
                return $availabilities->pluck('user_id')->toArray();
            });


        //  Song Leader (id_tugas = 2)
        $availableSongLeaders = Availability::whereBetween('date',  [$startDate, $endDate])
            ->whereHas('user', function ($query) {
                $query->where('id_tugas', 2);
            })
            ->get()
            ->groupBy('date') // Group by date
            ->map(function ($availabilities) {
                return $availabilities->pluck('user_id')->toArray();
            });

        // 3. Parameter untuk algoritma genetika
        $populationSize = 50;
        $generations = 100;
        $crossoverRate = 0.8;
        $mutationRate = 0.2;

        // 4. Inisialisasi dan jalankan algoritma genetika
        $geneticAlgorithm = new GeneticAlgorithm();
        $geneticAlgorithm->initialize(
            $availableMusicians,
            $availableSongLeaders,
            $populationSize,
            $generations,
            $crossoverRate,
            $mutationRate,
            $startDate,
            $endDate,
            $services
        );

        $bestSchedule = $geneticAlgorithm->run();


        // 5. Simpan jadwal terbaik ke database
        // Hapus dulu semua jadwal lama dalam rentang tanggal yang sama (opsional)
        JadwalPelayanan::whereBetween('date', [$startDate, $endDate])->delete();

        // Simpan jadwal baru
        foreach ($bestSchedule as $dateService => $assignments) {
            list($date, $service) = explode(' - ', $dateService);
            // Pastikan data yang disimpan valid
            if (isset($assignments['Pemusik'], $assignments['Song Leader 1'])) {
                JadwalPelayanan::create([
                    'date' => $date,
                    'service' =>  $service,
                    'musician_id' => $assignments['Pemusik'],
                    'song_leader1_id' => $assignments['Song Leader 1'],
                    'song_leader2_id' => $assignments['Song Leader 2'] ?? null, //Bisa Kosong
                    'status' => 0, // Menunggu konfirmasi
                ]);
            } else {
                //Handle error, misalnya log ke file
                Log::error('Data Penjadwalan Tidak Lengkap', ['bestSchedule' => $bestSchedule]);
            }
        }

        return response()->json(['message' => 'Jadwal berhasil dibuat dan disimpan.']);
    }
}
