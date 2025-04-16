<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\JadwalPelayanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JadwalPelayananController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $availabilities = Availability::where('user_id', $userId)->pluck('date');
        $jadwals = JadwalPelayanan::with(['pemusik', 'songLeader1', 'songLeader2'])
            ->where('id_pemusik', Auth::id())
            ->orWhere('id_sl1', Auth::id())
            ->orWhere('id_sl2', Auth::id())
            ->get();
        return view('user.jadwal-pelayanan.index', compact('availabilities', 'jadwals'));
    }

    public function store(Request $request)
    {
        try {
            // Periksa apakah request berisi 'dates'
            if (!$request->has('dates') || empty($request->input('dates'))) {
                return redirect()->back()->with('error', 'Silakan pilih setidaknya satu tanggal.');
            }

            // Pastikan input 'dates' berupa array (jika dikirim sebagai string, ubah ke array)
            $dates = is_array($request->input('dates')) ? $request->input('dates') : explode(',', $request->input('dates'));

            // Validasi setiap tanggal (pastikan format YYYY-MM-DD)
            foreach ($dates as $date) {
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                    return redirect()->back()->with('error', 'Format tanggal tidak valid.');
                }
            }

            $userId = Auth::id();

            // Simpan ke database hanya jika tanggal belum ada
            foreach ($dates as $date) {
                $existingAvailability = Availability::where('user_id', $userId)
                    ->where('date', $date) // Sesuaikan dengan nama kolom database
                    ->exists();

                if (!$existingAvailability) {
                    Availability::create([
                        'user_id' => $userId,
                        'date' => $date,
                    ]);
                }
            }

            return redirect()->route('user.jadwal-pelayanan')->with('success', 'Jadwal berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan jadwal: ' . $e->getMessage());
        }
    }
}
