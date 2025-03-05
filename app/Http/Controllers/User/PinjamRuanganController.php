<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Admin\ValidasiRuangController;
use App\Http\Controllers\Controller;

use App\Models\Jadwal;
use App\Models\PinjamRuangan;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PinjamRuanganController extends Controller
{
    public function list()
    {
        $rooms = Ruangan::all();
        // $pinjamJadwals = PinjamRuangan::where('status', 'Disetujui')->get()->map(function ($booking) {
        //     return [
        //         'title' => $booking->kegiatan,
        //         'start' => $booking->start_time,
        //         'end' => $booking->end_time,
        //         'description' => $booking->description,
        //         'color' => $booking->ruangan->color,
        //     ];
        // });

        $adminJadwals = Jadwal::all()->map(function ($jadwal) {
            return [
                'title' => $jadwal->title,
                'start' => $jadwal->start,
                'end' => $jadwal->end,
                'description' => $jadwal->description,
                'color' => $jadwal->color,
            ];
        });
        $jadwals = $adminJadwals;
        // $jadwals = $pinjamJadwals->merge($adminJadwals);

        return view('user.jadwal-ruangan', ['jadwals' => $jadwals, 'rooms' => $rooms]);
    }

    public function create()
    {
        $rooms = Ruangan::all();
        return view('bookings.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:ruangan,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        PinjamRuangan::create([
            'room_id' => $request->room_id,
            'user_id' => Auth::user()->id,
            'kegiatan' => $request->kegiatan,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'Diajukan',
        ]);

        return redirect()->route('jadwal-ruangan')->with('success', 'Pengajuan peminjaman berhasil dikirim!');
    }

    public function index()
    {
        $bookings = PinjamRuangan::with(['room', 'user'])->get();
        return view('bookings.index', compact('bookings'));
    }

    public function updateStatus(Request $request, PinjamRuangan $booking)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
        ]);

        $booking->update(['status' => $request->status]);

        // Jika status disetujui, tambahkan ke tabel jadwals
        if ($request->status === 'Disetujui') {
            // Cek apakah jadwal sudah ada di tabel jadwals untuk mencegah duplikasi
            $existingJadwal = Jadwal::where('title', $booking->kegiatan)
                ->where('start', $booking->start_time)
                ->where('end', $booking->end_time)
                ->first();

            if (!$existingJadwal) {
                Jadwal::create([
                    'title' => $booking->kegiatan,
                    'start' => $booking->start_time,
                    'end' => $booking->end_time,
                    'description' => 'Peminjam ruangan: ' . $booking->users->name,
                    'color' => $booking->ruangan->color,
                ]);
            }
        }
        return redirect()->route('bookings.index')->with('success', 'Status peminjaman berhasil diperbarui!');
    }
}
