<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPelayanan;
use App\Services\GeneticAlgorithm;
use Carbon\Carbon;

class KonfirmasiJadwalController extends Controller
{
    public function confirmSchedule(Request $request, $id)
    {
        $jadwal = JadwalPelayanan::findOrFail($id);

        // Jika user menerima
        if ($request->input('action') === 'accept') {
            $jadwal->update(['is_confirmed' => true]);
            return response()->json(['message' => 'Jadwal diterima.']);
        }

        // Jika user menolak
        if ($request->input('action') === 'reject') {
            $jadwal->update(['status' => 2]); // Status ditolak

            // Jalankan algoritma genetika lagi untuk membuat jadwal baru
            $geneticAlgorithm = new GeneticAlgorithm();
            $newSchedule = $geneticAlgorithm->run();

            // Simpan jadwal baru
            foreach ($newSchedule as $date => $assignments) {
                JadwalPelayanan::create([
                    'date' => $date,
                    'jadwal' => 'Ibadah', // Sesuaikan dengan jenis ibadah
                    'id_pemusik' => $assignments['Pemusik'],
                    'id_sl1' => $assignments['Song Leader 1'],
                    'id_sl2' => $assignments['Song Leader 2'],
                    'status' => 0, // Menunggu konfirmasi
                    'confirmation_deadline' => Carbon::now()->addDays(3), // Batas waktu 3 hari
                ]);
            }

            return response()->json(['message' => 'Jadwal ditolak. Jadwal baru sedang dibuat.']);
        }

        return response()->json(['message' => 'Aksi tidak valid.'], 400);
    }
}