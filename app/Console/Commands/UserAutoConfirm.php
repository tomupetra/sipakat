<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JadwalPelayanan;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Log;

class UserAutoConfirm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-auto-confirm';

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
        try {
            // Ambil jadwal yang belum dikonfirmasi dan sudah melewati batas waktu
            $schedules = JadwalPelayanan::where('is_confirmed', false)
                ->where('confirmation_deadline', '<=', Carbon::now())
                ->get();

            if ($schedules->isEmpty()) {
                $this->info('Tidak ada jadwal yang perlu dikonfirmasi otomatis.');
                Log::info('Auto Confirm: Tidak ada jadwal yang perlu dikonfirmasi otomatis.');
                return;
            }

            $count = 0;
            foreach ($schedules as $schedule) {
                // Update status konfirmasi
                $schedule->update([
                    'is_confirmed' => true,
                    'status' => 1 // Status 1 = Diterima
                ]);

                $count++;

                // Log informasi
                Log::info("Jadwal ID {$schedule->id} otomatis dikonfirmasi", [
                    'tanggal' => $schedule->date,
                    'pemusik' => $schedule->id_pemusik,
                    'song_leader_1' => $schedule->id_sl1,
                    'song_leader_2' => $schedule->id_sl2
                ]);
            }

            $this->info("Berhasil mengonfirmasi otomatis {$count} jadwal.");
            Log::info("Auto Confirm: Berhasil mengonfirmasi {$count} jadwal secara otomatis.");
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
            Log::error('Auto Confirm Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
