<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ScheduleService;
use App\Models\JadwalPelayanan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PelayananController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    // Metode untuk membuat jadwal
    public function generateSchedule()
    {
        try {
            // Menggunakan ScheduleService untuk menghasilkan jadwal
            $this->scheduleService->generateSchedule();

            return redirect()->back()->with('success', 'Jadwal berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function index()
    {
        $jadwals = JadwalPelayanan::whereMonth('date', now()->month)
            ->orderBy('date')
            ->get();

        return view('admin.pelayanan.index', compact('jadwals'));
    }

    public function edit($id)
    {
        $jadwal = JadwalPelayanan::findOrFail($id);

        $keyboardists = User::where('id_tugas', 1)->get();  // Pengguna dengan id_tugas = 1 adalah pemusik
        $songLeaders = User::where('id_tugas', 2)->get();   // Pengguna dengan id_tugas = 2 adalah song leader

        return view('admin.pelayanan.edit', compact('jadwal', 'keyboardists', 'songLeaders'));
    }


    public function update(Request $request, $id)
    {
        // Validasi data yang dimasukkan
        $request->validate([
            'date' => 'required|date',
            'jadwal' => 'required|in:07:00,10:00,18:00',
            'id_pemusik' => 'required|exists:users,id',
            'id_sl1' => 'required|exists:users,id',
            'id_sl2' => 'required|exists:users,id',
        ]);

        // Ambil data jadwal yang ingin diperbarui
        $jadwal = JadwalPelayanan::findOrFail($id);

        // Perbarui data jadwal
        $jadwal->update([
            'date' => $request->date,
            'jadwal' => $request->jadwal,
            'id_pemusik' => $request->id_pemusik,
            'id_sl1' => $request->id_sl1,
            'id_sl2' => $request->id_sl2,
        ]);

        // Redirect ke halaman daftar jadwal setelah berhasil update
        return redirect()->route('admin.jadwal-pelayanan')->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jadwal = JadwalPelayanan::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('admin.jadwal-pelayanan')->with('success', 'Jadwal berhasil dihapus.');
    }

    public function showSchedule()
    {
        // Ambil jadwal yang relevan untuk pengguna yang sedang login
        $jadwals = JadwalPelayanan::with(['pemusik', 'songLeader1', 'songLeader2'])
            ->where('id_pemusik', Auth::id())
            ->orWhere('id_sl1', Auth::id())
            ->orWhere('id_sl2', Auth::id())
            ->get();

        return view('user.schedule', compact('jadwals'));
    }
}
