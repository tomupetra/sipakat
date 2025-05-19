<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\PinjamRuangan;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\LaporanPinjamRuangan;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalController extends Controller
{
    public function index()
    {
        $rooms = Ruangan::all();
        return view('/admin/ruangan/jadwal', compact('rooms'));
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date_format:Y-m-d\TH:i',
            'end' => 'required|date_format:Y-m-d\TH:i|after_or_equal:start',
            'description' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $validated['start'] = Carbon::parse($validated['start'])->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
        $validated['end'] = Carbon::parse($validated['end'])->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
        // $validated['color'] = $validated['color'] ?? '#000000';

        Jadwal::create($validated);

        return response()->json(['message' => 'Jadwal berhasil ditambahkan.'], 200);
    }

    public function getEvents()
    {
        $events = Jadwal::all()->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => Carbon::parse($event->start)->setTimezone('Asia/Jakarta')->toIso8601String(),
                'end' => Carbon::parse($event->end)->setTimezone('Asia/Jakarta')->toIso8601String(),
                'color' => $event->color,
                'description' => $event->description,
            ];
        });

        return response()->json($events);
    }

    public function deleteEvent($id)
    {
        try {
            $event = Jadwal::findOrFail($id);
            $pinjamRuangan = PinjamRuangan::where('id', $event->id)->first();
            if ($pinjamRuangan) {
                $pinjamRuangan->delete();
            }
            $event->delete();
            return response()->json(['message' => 'Event deleted successfully.'], 200);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus jadwal:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal menghapus jadwal.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'start' => 'required|date_format:Y-m-d\TH:i',
                'end' => 'required|date_format:Y-m-d\TH:i|after_or_equal:start',
                'description' => 'nullable|string',
                'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            ]);

            $validated['start'] = Carbon::parse($validated['start'])->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
            $validated['end'] = Carbon::parse($validated['end'])->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');

            $schedule = Jadwal::findOrFail($id);
            $schedule->update($validated);

            return response()->json(['message' => 'Jadwal berhasil diperbarui.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui jadwal.', 'error' => $e->getMessage()], 500);
        }
    }

    public function search(Request $request)
    {
        $searchKeywords = $request->input('title');

        $matchingEvents = Jadwal::where('title', 'like', '%' . $searchKeywords . '%')->get();

        return response()->json($matchingEvents);
    }

    protected function getFilteredLaporanRuangan(Request $request)
    {
        $query = PinjamRuangan::query();

        // Join ke tabel ruangan dan user untuk mendapatkan nama
        $query->with(['ruangan', 'user']);

        // Filter berdasarkan tanggal (start_time)
        if ($request->filled('tanggal')) {
            $tanggal = \Carbon\Carbon::parse($request->tanggal)->toDateString();
            $query->whereDate('start_time', $tanggal);
        }

        // Filter berdasarkan ruangan (nama ruangan)
        if ($request->filled('ruangan')) {
            $query->whereHas('ruangan', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->ruangan}%");
            });
        }

        // Filter berdasarkan bulan (format: YYYY-MM)
        if ($request->filled('bulan')) {
            [$year, $month] = explode('-', $request->bulan);
            $query->whereYear('date', $year)->whereMonth('date', $month);
        }

        // Pencarian berdasarkan nama peminjam, ruangan, atau kegiatan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%");
                })
                    ->orWhereHas('ruangan', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })
                    ->orWhere('kegiatan', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%");
            });
        }

        return $query->orderBy('start_time', 'desc');
    }

    public function laporanRuangan(Request $request)
    {
        $laporan = $this->getFilteredLaporanRuangan($request)->paginate(20)->withQueryString();
        return view('admin.ruangan.laporan-ruangan', compact('laporan'));
    }

    public function exportPdf(Request $request)
    {
        $laporan = $this->getFilteredLaporanRuangan($request)->get();
        $pdf = Pdf::loadView('admin.ruangan.pdf_laporan', compact('laporan'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('laporan_peminjaman_ruangan.pdf');
    }

    // User View
    public function userView()
    {
        try {
            $jadwals = Jadwal::all(['id', 'title', 'start', 'end', 'description', 'color']);
            return view('user.jadwal-ruangan', compact('jadwals'));
        } catch (\Exception $e) {
            return view('user.jadwal-ruangan')->with('message', 'Gagal mengambil jadwal.');
        }
    }
}
