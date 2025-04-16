<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Availability;
use App\Models\User;
use Carbon\Carbon;

class AvailabilitySeeder extends Seeder
{
    public function run()
    {
        // Tentukan tahun dan bulan saat ini
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Ambil semua pengguna dari tabel users yang berperan sebagai keyboardist dan song leader
        $keyboardists = User::where('id_tugas', 1)->get(); // Mengambil semua keyboardist
        $songLeaders = User::where('id_tugas', 2)->get(); // Mengambil semua song leader

        // Cek apakah jumlah keyboardist dan song leaders cukup
        if ($keyboardists->count() < 5 || $songLeaders->count() < 7) {
            $this->command->info('Pastikan ada setidaknya 5 keyboardist dan 7 song leader di database.');
            return;
        }

        // Dapatkan semua hari Minggu dalam bulan saat ini
        $sundayDates = $this->getSundaysInMonth($currentYear, $currentMonth);

        // Loop untuk setiap hari Minggu
        foreach ($sundayDates as $sundayDate) {
            // Loop untuk keyboardist
            foreach ($keyboardists as $keyboardist) {
                $this->createAvailability($keyboardist, $sundayDate);
            }

            // Loop untuk song leader
            foreach ($songLeaders as $songLeader) {
                $this->createAvailability($songLeader, $sundayDate);
            }
        }

        // Validasi jumlah availability
        $weeksInMonth = count($sundayDates); // Jumlah minggu (hari Minggu)
        $minKeyboardistAvailability = $weeksInMonth * 3; // 3 sesi per minggu
        $minSongLeaderAvailability = $weeksInMonth * 3 * 2; // 3 sesi × 2 song leader per minggu

        $this->validateAvailability($keyboardists, $minKeyboardistAvailability, 'Keyboardist');
        $this->validateAvailability($songLeaders, $minSongLeaderAvailability, 'Song Leader');

        $this->command->info("Seeder Availability berhasil dijalankan untuk bulan {$currentMonth} tahun {$currentYear}.");
    }

    /**
     * Mendapatkan semua hari Minggu dalam sebuah bulan tertentu.
     *
     * @param int $year
     * @param int $month
     * @return \Carbon\Carbon[]
     */
    private function getSundaysInMonth($year, $month)
    {
        $sundayDates = [];
        $firstDayOfMonth = Carbon::create($year, $month, 1);
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();

        $currentSunday = $firstDayOfMonth->copy()->startOfWeek(Carbon::SUNDAY);

        // Pastikan minggu pertama berada di bulan yang dimaksud
        if ($currentSunday->month < $month) {
            $currentSunday->addWeek();
        }

        while ($currentSunday <= $lastDayOfMonth) {
            $sundayDates[] = $currentSunday->copy();
            $currentSunday->addWeek();
        }

        return $sundayDates;
    }

    /**
     * Helper method untuk membuat atau memperbarui availability.
     *
     * @param \App\Models\User $user
     * @param \Carbon\Carbon $date
     */
    private function createAvailability($user, $date)
    {
        Availability::firstOrCreate([
            'user_id' => $user->id,
            'date' => $date->toDateString(),
        ]);
    }

    /**
     * Validasi jumlah availability untuk setiap pengguna.
     *
     * @param \Illuminate\Support\Collection $users
     * @param int $minAvailability
     * @param string $role
     */
    private function validateAvailability($users, $minAvailability, $role)
    {
        foreach ($users as $user) {
            $availabilityCount = Availability::where('user_id', $user->id)->count();
            if ($availabilityCount < $minAvailability) {
                $this->command->warn("{$role} dengan ID {$user->id} hanya memiliki {$availabilityCount} availability. Minimum diperlukan {$minAvailability}.");
            }
        }
    }
}
