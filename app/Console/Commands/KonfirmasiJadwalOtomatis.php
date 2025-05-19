<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JadwalPelayanan;
use Carbon\Carbon;

class KonfirmasiJadwalOtomatis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:konfirmasi-jadwal-otomatis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Konfirmasi Jadwal Pelayanan Otomatis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil semua jadwal yang belum dikonfirmasi dan sudah melewati deadline
        $jadwals = JadwalPelayanan::where('status_pemusik', 0)
            ->where('status_sl1', 0)
            ->where('status_sl2', 0)
            ->where('confirmation_deadline', '<', Carbon::now())
            ->get();

        foreach ($jadwals as $jadwal) {
            $jadwal->update([
                'status_pemusik' => 1,
                'status_sl1' => 1,
                'status_sl2' => 1,
                'is_confirmed' => 1,
                'is_locked' => true, // Kunci jadwal setelah semua mengonfirmasi
            ]);

            // Simpan ke history
            $this->saveToHistory($jadwal);
        }

        $this->info('Jadwal pelayanan yang melewati deadline telah dikonfirmasi secara otomatis.');
    }


    private function saveToHistory($jadwal)
    {
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
    }
}
