<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InsertHistoryJadwalPelayanan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:insert-history-jadwal-pelayanan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $firstDayOfThisMonth = $now->copy()->startOfMonth();

        // Ambil semua jadwal yang tanggalnya sebelum bulan ini
        $jadwals = \App\Models\JadwalPelayanan::where('date', '<', $firstDayOfThisMonth)->get();

        foreach ($jadwals as $jadwal) {
            \App\Models\HistoryJadwalPelayanan::create([
                'jadwal_pelayanan_id' => $jadwal->id,
                'date' => $jadwal->date,
                'jadwal' => $jadwal->jadwal,
                'id_pemusik' => $jadwal->id_pemusik,
                'id_sl1' => $jadwal->id_sl1,
                'id_sl2' => $jadwal->id_sl2,
                'is_confirmed' => $jadwal->is_confirmed,
                'is_locked' => $jadwal->is_locked,
            ]);
            $jadwal->delete();
        }

        $this->info('Jadwal bulan yang sudah lewat berhasil dipindahkan ke history.');
    }
}
