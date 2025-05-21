<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalPelayanan;
use App\Models\PinjamRuangan;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Pastikan mengambil user yang login dengan id tersebut dari database (fresh)
        $user = \App\Models\User::find($user->id);

        // Total jadwal pelayanan (semua jadwal di mana user terlibat)
        $totalJadwalPelayanan = JadwalPelayanan::where(function ($q) use ($user) {
            $q->where('id_pemusik', $user->id)
                ->orWhere('id_sl1', $user->id)
                ->orWhere('id_sl2', $user->id);
        })->count();

        // Jadwal pelayanan yang belum dikonfirmasi (status_pemusik/sl1/sl2 = 0)
        $jadwalPelayananBelumKonfirmasi = JadwalPelayanan::where(function ($q) use ($user) {
            $q->where(function ($q2) use ($user) {
                $q2->where('id_pemusik', $user->id)->where('status_pemusik', 0);
            })->orWhere(function ($q2) use ($user) {
                $q2->where('id_sl1', $user->id)->where('status_sl1', 0);
            })->orWhere(function ($q2) use ($user) {
                $q2->where('id_sl2', $user->id)->where('status_sl2', 0);
            });
        })->count();

        // Jadwal ruangan yang sudah dikonfirmasi (status = 'Disetujui')
        $jadwalRuanganDikonfirmasi = PinjamRuangan::where('user_id', $user->id)
            ->where('status', 'Disetujui')
            ->count();

        // Jadwal ruangan yang belum dikonfirmasi (status = 'Diajukan' atau 'Menunggu')
        $jadwalRuanganBelumKonfirmasi = PinjamRuangan::where('user_id', $user->id)
            ->whereIn('status', ['Ditolak'])
            ->count();

        return view('user.index', compact(
            'totalJadwalPelayanan',
            'jadwalPelayananBelumKonfirmasi',
            'jadwalRuanganDikonfirmasi',
            'jadwalRuanganBelumKonfirmasi'
        ));
    }

    public function jadwalRuangan()
    {
        return view('user/jadwal-ruangan');
    }
}
