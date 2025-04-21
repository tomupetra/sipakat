<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\JadwalPelayanan;
use App\Models\LaporanPelayanan;
use Carbon\Carbon;
use App\Models\HistoryJadwalPelayanan;
use Barryvdh\DomPDF\Facade\Pdf;

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
            $this->saveToHistory($jadwal); // Simpan ke history
        }
        // Cek jika masih menunggu konfirmasi
        else {
            $jadwal->update([
                'is_confirmed' => 0,
            ]);
        }
    }

    public function getLaporan(Request $request)
    {
        $query = JadwalPelayanan::with(['pemusik', 'songLeader1', 'songLeader2']);

        // Filter berdasarkan tanggal jika diberikan
        if ($request->filled('tanggal')) {
            $tanggal = Carbon::parse($request->tanggal);
            $query->where(function ($q) use ($tanggal) {
                $q->where('date', '<', $tanggal)
                    ->orWhere('is_confirmed', 1);
            });
        } else {
            // Default: tanggal hari ini
            $query->where(function ($q) {
                $q->where('date', '<', Carbon::today())
                    ->orWhere('is_confirmed', 1);
            });
        }

        // Filter berdasarkan bulan (format: YYYY-MM)
        if ($request->filled('bulan')) {
            [$year, $month] = explode('-', $request->bulan);
            $query->whereYear('date', $year)->whereMonth('date', $month);
        }

        // Pencarian berdasarkan nama atau sesi
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('jadwal', 'like', '%' . $search . '%')
                    ->orWhereHas('pemusik', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('songLeader1', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('songLeader2', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $laporan = $query->orderBy('date', 'desc')->paginate(15)->withQueryString();

        return view('admin.pelayanan.laporan', compact('laporan'));
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

    private function saveToHistory($jadwal)
    {
        HistoryJadwalPelayanan::create([
            'jadwal_pelayanan_id' => $jadwal->id,
            'date' => $jadwal->date,
            'jadwal' => $jadwal->jadwal,
            'id_pemusik' => $jadwal->id_pemusik,
            'id_sl1' => $jadwal->id_sl1,
            'id_sl2' => $jadwal->id_sl2,
            'is_confirmed' => $jadwal->is_confirmed,
            'is_locked' => $jadwal->is_locked,
        ]);
    }

    protected function getFilteredLaporan(Request $request)
    {
        $query = JadwalPelayanan::with(['pemusik', 'songLeader1', 'songLeader2']);

        if ($request->filled('tanggal')) {
            $tanggal = \Carbon\Carbon::parse($request->tanggal);
            $query->where(function ($q) use ($tanggal) {
                $q->where('date', '<', $tanggal)
                    ->orWhere('is_confirmed', 1);
            });
        } else {
            $query->where(function ($q) {
                $q->where('date', '<', \Carbon\Carbon::today())
                    ->orWhere('is_confirmed', 1);
            });
        }

        if ($request->filled('bulan')) {
            [$year, $month] = explode('-', $request->bulan);
            $query->whereYear('date', $year)->whereMonth('date', $month);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('jadwal', 'like', '%' . $search . '%')
                    ->orWhereHas('pemusik', fn($q) => $q->where('name', 'like', "%$search%"))
                    ->orWhereHas('songLeader1', fn($q) => $q->where('name', 'like', "%$search%"))
                    ->orWhereHas('songLeader2', fn($q) => $q->where('name', 'like', "%$search%"));
            });
        }

        return $query->orderBy('date', 'desc');
    }


    public function exportPdf(Request $request)
    {
        $laporan = $this->getFilteredLaporan($request)->get();

        $pdf = PDF::loadView('admin.pelayanan.laporan_pdf', compact('laporan'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan_pelayanan.pdf');
    }
}
