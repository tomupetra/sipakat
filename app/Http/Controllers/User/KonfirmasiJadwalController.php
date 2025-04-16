<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\JadwalPelayanan;
use App\Models\LaporanPelayanan;
use Carbon\Carbon;

class KonfirmasiJadwalController extends Controller
{
    // Fungsi untuk mengonfirmasi jadwal
    public function confirmSchedule($id)
    {
        $jadwal = JadwalPelayanan::findOrFail($id); // Ambil jadwal berdasarkan ID
        $user = Auth::user(); // Ambil pengguna yang sedang login

        // Cek apakah pengguna yang sedang login adalah pemusik atau song leader
        if ($user->id == $jadwal->id_pemusik) {
            // Pastikan jika belum lewat deadline konfirmasi
            if ($jadwal->confirmation_deadline && Carbon::now()->lessThanOrEqualTo($jadwal->confirmation_deadline)) {
                // Update status konfirmasi menjadi 'Diterima' untuk pemusik
                $jadwal->update([
                    'status_pemusik' => 1, // Status untuk pemusik (Diterima)
                ]);

                // Periksa status konfirmasi keseluruhan
                $this->updateOverallConfirmationStatus($jadwal);

                return redirect()->route('user.jadwal-pelayanan')->with('success', 'Jadwal berhasil dikonfirmasi sebagai pemusik!');
            }

            return redirect()->route('user.jadwal-pelayanan')->with('error', 'Batas waktu konfirmasi telah lewat.');
        } elseif ($user->id == $jadwal->id_sl1) {
            // Cek apakah pengguna yang login adalah Song Leader 1
            if ($jadwal->confirmation_deadline && Carbon::now()->lessThanOrEqualTo($jadwal->confirmation_deadline)) {
                // Update status konfirmasi menjadi 'Diterima' untuk Song Leader 1
                $jadwal->update([
                    'status_sl1' => 1, // Status untuk SL1 (Diterima)
                ]);

                // Periksa status konfirmasi keseluruhan
                $this->updateOverallConfirmationStatus($jadwal);

                return redirect()->route('user.jadwal-pelayanan')->with('success', 'Jadwal berhasil dikonfirmasi sebagai Song Leader 1!');
            }

            return redirect()->route('user.jadwal-pelayanan')->with('error', 'Batas waktu konfirmasi telah lewat.');
        } elseif ($user->id == $jadwal->id_sl2) {
            // Cek apakah pengguna yang login adalah Song Leader 2
            if ($jadwal->confirmation_deadline && Carbon::now()->lessThanOrEqualTo($jadwal->confirmation_deadline)) {
                // Update status konfirmasi menjadi 'Diterima' untuk Song Leader 2
                $jadwal->update([
                    'status_sl2' => 1, // Status untuk SL2 (Diterima)
                ]);

                // Periksa status konfirmasi keseluruhan
                $this->updateOverallConfirmationStatus($jadwal);

                return redirect()->route('user.jadwal-pelayanan')->with('success', 'Jadwal berhasil dikonfirmasi sebagai Song Leader 2!');
            }

            return redirect()->route('user.jadwal-pelayanan')->with('error', 'Batas waktu konfirmasi telah lewat.');
        }

        return redirect()->route('user.jadwal-pelayanan')->with('error', 'Anda tidak dapat mengonfirmasi jadwal ini.');
    }

    // Fungsi untuk menolak jadwal
    public function rejectSchedule($id)
    {
        $jadwal = JadwalPelayanan::findOrFail($id); // Ambil jadwal berdasarkan ID
        $user = Auth::user(); // Ambil pengguna yang sedang login

        // Cek apakah pengguna yang sedang login adalah pemusik atau song leader
        if ($user->id == $jadwal->id_pemusik) {
            // Pastikan jika belum lewat deadline konfirmasi
            if ($jadwal->confirmation_deadline && Carbon::now()->lessThanOrEqualTo($jadwal->confirmation_deadline)) {
                // Update status konfirmasi menjadi 'Ditolak' untuk pemusik
                $jadwal->update([
                    'status_pemusik' => 2, // Status untuk pemusik (Ditolak)
                ]);

                // Periksa status konfirmasi keseluruhan
                $this->updateOverallConfirmationStatus($jadwal);

                return redirect()->route('user.jadwal-pelayanan')->with('success', 'Jadwal berhasil ditolak sebagai pemusik!');
            }

            return redirect()->route('user.jadwal-pelayanan')->with('error', 'Batas waktu konfirmasi telah lewat.');
        } elseif ($user->id == $jadwal->id_sl1) {
            // Cek apakah pengguna yang login adalah Song Leader 1
            if ($jadwal->confirmation_deadline && Carbon::now()->lessThanOrEqualTo($jadwal->confirmation_deadline)) {
                // Update status konfirmasi menjadi 'Ditolak' untuk Song Leader 1
                $jadwal->update([
                    'status_sl1' => 2, // Status untuk SL1 (Ditolak)
                ]);

                // Periksa status konfirmasi keseluruhan
                $this->updateOverallConfirmationStatus($jadwal);

                return redirect()->route('user.jadwal-pelayanan')->with('success', 'Jadwal berhasil ditolak sebagai Song Leader 1!');
            }

            return redirect()->route('user.jadwal-pelayanan')->with('error', 'Batas waktu konfirmasi telah lewat.');
        } elseif ($user->id == $jadwal->id_sl2) {
            // Cek apakah pengguna yang login adalah Song Leader 2
            if ($jadwal->confirmation_deadline && Carbon::now()->lessThanOrEqualTo($jadwal->confirmation_deadline)) {
                // Update status konfirmasi menjadi 'Ditolak' untuk Song Leader 2
                $jadwal->update([
                    'status_sl2' => 2, // Status untuk SL2 (Ditolak)
                ]);

                // Periksa status konfirmasi keseluruhan
                $this->updateOverallConfirmationStatus($jadwal);

                return redirect()->route('user.jadwal-pelayanan')->with('success', 'Jadwal berhasil ditolak sebagai Song Leader 2!');
            }

            return redirect()->route('user.jadwal-pelayanan')->with('error', 'Batas waktu konfirmasi telah lewat.');
        }

        return redirect()->route('user.jadwal-pelayanan')->with('error', 'Anda tidak dapat menolak jadwal ini.');
    }

    // Fungsi untuk memeriksa dan memperbarui status keseluruhan konfirmasi
    private function updateOverallConfirmationStatus($jadwal)
    {
        // Cek apakah ada yang menolak
        if ($jadwal->status_pemusik == 2 || $jadwal->status_sl1 == 2 || $jadwal->status_sl2 == 2) {
            $jadwal->update([
                'is_confirmed' => 2,
            ]);
        }
        // Cek apakah semua sudah mengonfirmasi dan menerima
        elseif ($jadwal->status_pemusik == 1 && $jadwal->status_sl1 == 1 && $jadwal->status_sl2 == 1) {
            $jadwal->update([
                'is_confirmed' => 1,
                'is_locked' => true, // Kunci jadwal setelah semua mengonfirmasi
            ]);

            $this->saveToLaporan($jadwal); // Simpan ke laporan pelayanan
        }
        // Cek jika masih menunggu konfirmasi
        else {
            $jadwal->update([
                'is_confirmed' => 0,
            ]);
        }
    }

    // Fungsi untuk menyimpan laporan saat jadwal terkunci
    private function saveToLaporan($jadwal)
    {
        LaporanPelayanan::create([
            'tanggal' => $jadwal->date,
            'sesi' => $jadwal->jadwal,
            'pemusik' => $jadwal->pemusik->name,
            'sl1' => $jadwal->songLeader1->name,
            'sl2' => $jadwal->songLeader2->name,
            'is_confirmed' => $jadwal->is_confirmed,
            'is_locked' => $jadwal->is_locked,
        ]);
    }
}
