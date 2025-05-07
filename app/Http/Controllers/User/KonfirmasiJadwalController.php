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
use App\Models\User;
use App\Notifications\NotifikasiJadwalBaru;

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

                // Ganti pemusik dengan yang lain
                $this->replaceMusician($jadwal);

                return redirect()->route('user.jadwal-pelayanan')->with('success', 'Jadwal berhasil ditolak dan pemusik diganti!');
            }

            return redirect()->route('user.jadwal-pelayanan')->with('error', 'Batas waktu konfirmasi telah lewat.');
        } elseif ($user->id == $jadwal->id_sl1) {
            // Cek apakah pengguna yang login adalah Song Leader 1
            if ($jadwal->confirmation_deadline && Carbon::now()->lessThanOrEqualTo($jadwal->confirmation_deadline)) {
                // Update status konfirmasi menjadi 'Ditolak' untuk Song Leader 1
                $jadwal->update([
                    'status_sl1' => 2, // Status untuk SL1 (Ditolak)
                ]);

                // Ganti Song Leader 1 dengan yang lain
                $this->replaceSongLeader1($jadwal);

                return redirect()->route('user.jadwal-pelayanan')->with('success', 'Jadwal berhasil ditolak dan Song Leader 1 diganti!');
            }

            return redirect()->route('user.jadwal-pelayanan')->with('error', 'Batas waktu konfirmasi telah lewat.');
        } elseif ($user->id == $jadwal->id_sl2) {
            // Cek apakah pengguna yang login adalah Song Leader 2
            if ($jadwal->confirmation_deadline && Carbon::now()->lessThanOrEqualTo($jadwal->confirmation_deadline)) {
                // Update status konfirmasi menjadi 'Ditolak' untuk Song Leader 2
                $jadwal->update([
                    'status_sl2' => 2, // Status untuk SL2 (Ditolak)
                ]);

                // Ganti Song Leader 2 dengan yang lain
                $this->replaceSongLeader2($jadwal);

                return redirect()->route('user.jadwal-pelayanan')->with('success', 'Jadwal berhasil ditolak dan Song Leader 2 diganti!');
            }

            return redirect()->route('user.jadwal-pelayanan')->with('error', 'Batas waktu konfirmasi telah lewat.');
        }

        return redirect()->route('user.jadwal-pelayanan')->with('error', 'Anda tidak dapat menolak jadwal ini.');
    }

    private function replaceMusician($jadwal)
    {
        $availableMusicians = User::where('id_tugas', 1)
            ->where('id', '!=', $jadwal->id_pemusik)
            ->whereHas('availabilities', function ($query) use ($jadwal) {
                $query->where('date', $jadwal->date);
            })
            ->get();

        if ($availableMusicians->isEmpty()) {
            $availableMusicians = User::where('id_tugas', 1)
                ->where('id', '!=', $jadwal->id_pemusik)
                ->get();
        }

        if ($availableMusicians->isNotEmpty()) {
            $newMusician = $availableMusicians->random();
            $jadwal->update(['id_pemusik' => $newMusician->id]);

            // Set status_pemusik to 0
            $jadwal->update(['status_pemusik' => 0]);

            // Kirim notifikasi ke pemusik pengganti
            $newMusician->notify(new NotifikasiJadwalBaru($jadwal));
        }
    }

    private function replaceSongLeader1($jadwal)
    {
        $availableSongLeaders = User::where('id_tugas', 2)
            ->where('id', '!=', $jadwal->id_sl1)
            ->whereHas('availabilities', function ($query) use ($jadwal) {
                $query->where('date', $jadwal->date);
            })
            ->get();

        if ($availableSongLeaders->isEmpty()) {
            $availableSongLeaders = User::where('id_tugas', 2)
                ->where('id', '!=', $jadwal->id_sl1)
                ->get();
        }

        if ($availableSongLeaders->isNotEmpty()) {
            $newSongLeader1 = $availableSongLeaders->random();
            $jadwal->update(['id_sl1' => $newSongLeader1->id]);

            // Set status_sl1 to 0
            $jadwal->update(['status_sl1' => 0]);

            // Kirim notifikasi ke Song Leader 1 pengganti
            $newSongLeader1->notify(new NotifikasiJadwalBaru($jadwal));
        }
    }

    private function replaceSongLeader2($jadwal)
    {
        $availableSongLeaders = User::where('id_tugas', 2)
            ->where('id', '!=', $jadwal->id_sl2)
            ->whereHas('availabilities', function ($query) use ($jadwal) {
                $query->where('date', $jadwal->date);
            })
            ->get();

        if ($availableSongLeaders->isEmpty()) {
            $availableSongLeaders = User::where('id_tugas', 2)
                ->where('id', '!=', $jadwal->id_sl2)
                ->get();
        }

        if ($availableSongLeaders->isNotEmpty()) {
            $newSongLeader2 = $availableSongLeaders->random();
            $jadwal->update(['id_sl2' => $newSongLeader2->id]);

            // Set status_sl2 to 0
            $jadwal->update(['status_sl2' => 0]);

            // Kirim notifikasi ke Song Leader 2 pengganti
            $newSongLeader2->notify(new NotifikasiJadwalBaru($jadwal));
        }
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

    public function saveToHistory()
    {
        $jadwals = JadwalPelayanan::where('date', '<', Carbon::today())
            ->where('is_confirmed', 1)
            ->get();

        foreach ($jadwals as $jadwal) {
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

            // Optionally, delete or mark the original schedule as archived
            // $jadwal->delete();
        }
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

    public function updateSchedule(Request $request, $id)
    {
        $jadwal = JadwalPelayanan::findOrFail($id);

        if (!$jadwal->canBeModified()) {
            return redirect()->back()->with('error', 'Jadwal tidak dapat diubah.');
        }

        // Lakukan update jadwal di sini
        // $jadwal->update([...]);

        return redirect()->route('user.jadwal-pelayanan')->with('success', 'Jadwal berhasil diubah.');
    }

    public function deleteSchedule($id)
    {
        $jadwal = JadwalPelayanan::findOrFail($id);

        if (!$jadwal->canBeModified()) {
            return redirect()->back()->with('error', 'Jadwal tidak dapat dihapus.');
        }

        $jadwal->delete();

        return redirect()->route('user.jadwal-pelayanan')->with('success', 'Jadwal berhasil dihapus.');
    }
}
