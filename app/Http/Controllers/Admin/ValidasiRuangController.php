<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PinjamRuangan;
use App\Models\Jadwal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ValidasiRuangController extends Controller
{
    public function index()
    {
        $bookings = PinjamRuangan::with(['ruangan', 'user'])->get();
        return view('/admin/ruangan/validasi', compact('bookings'));
    }

    public function create(Request $request)
    {
        $item = new PinjamRuangan();
        $item->title = $request->title;
        $item->start = $request->start;
        $item->end = $request->end;
        $item->description = $request->description;
        $item->color = $request->color;
        $item->save();

        return redirect('/fullcalender');
    }


    public function getEvents()
    {
        $schedules = PinjamRuangan::all();
        return response()->json($schedules);
    }

    public function deleteEvent($id)
    {
        $schedule = PinjamRuangan::findOrFail($id);
        $schedule->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }

    public function update(Request $request, $id)
    {
        $schedule = PinjamRuangan::findOrFail($id);

        $schedule->update([
            'start' => Carbon::parse($request->input('start_date'))->setTimezone('UTC'),
            'end' => Carbon::parse($request->input('end_date'))->setTimezone('UTC'),
        ]);

        return response()->json(['message' => 'Event moved successfully']);
    }

    public function resize(Request $request, $id)
    {
        $schedule = PinjamRuangan::findOrFail($id);

        $newEndDate = Carbon::parse($request->input('end_date'))->setTimezone('UTC');
        $schedule->update(['end' => $newEndDate]);

        return response()->json(['message' => 'Event resized successfully.']);
    }

    public function search(Request $request)
    {
        $searchKeywords = $request->input('title');

        $matchingEvents = PinjamRuangan::where('title', 'like', '%' . $searchKeywords . '%')->get();

        return response()->json($matchingEvents);
    }


    // Validasi Ruangan

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
                    'description' => 'Dipinjam oleh ' . $booking->user->name,
                    'color' => $booking->ruangan->color,
                ]);
            }
        }
        return redirect()->route('admin.bookings.index')->with('success', 'Status peminjaman berhasil diperbarui!');
    }

    public function approve($id)
    {
        $booking = PinjamRuangan::findOrFail($id);
        $booking->update(['status' => 'approved']);

        return redirect('/bookings')->with('success', 'Booking telah disetujui.');
    }

    public function reject($id)
    {
        $booking = PinjamRuangan::findOrFail($id);
        $booking->update(['status' => 'rejected']);

        return redirect('/bookings')->with('error', 'Booking telah ditolak.');
    }
}
