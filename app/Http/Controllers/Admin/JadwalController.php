<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\PinjamRuangan;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{
    public function index()
    {
        // $bookings = PinjamRuangan::where('status', 'Disetujui')
        //     ->with(['ruangan', 'user'])
        //     ->get();
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

    public function resize(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'start' => 'required|date_format:Y-m-d\TH:i:sP',
                'end' => 'required|date_format:Y-m-d\TH:i:sP|after_or_equal:start',
            ]);

            $validated['start'] = Carbon::parse($validated['start'])->setTimezone('Asia/Jakarta');
            $validated['end'] = Carbon::parse($validated['end'])->setTimezone('Asia/Jakarta');

            $schedule = Jadwal::findOrFail($id);
            $schedule->update($validated);

            return response()->json(['message' => 'Jadwal berhasil diperbarui.', 'data' => $schedule], 200);
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
