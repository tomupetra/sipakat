<?php

namespace App\Services;

use App\Models\User;
use App\Models\Availability;
use App\Models\HistoryJadwalPelayanan;
use App\Models\JadwalPelayanan;
use Carbon\Carbon;
use App\Notifications\NotifikasiJadwalBaru;

class ScheduleService
{
    public function generateSchedule()
    {
        $sessions = ['07:00', '10:00', '18:00'];
        $keyboardists = User::with('availabilities')->where('id_tugas', 1)->get();
        $songLeaders = User::with('availabilities')->where('id_tugas', 2)->get();

        $dates = Availability::distinct('date')
            ->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->pluck('date');

        $previousAssignments = ['keyboardists' => [], 'songLeaders' => []];
        $assignmentCount = [];

        foreach ($dates as $date) {
            if (JadwalPelayanan::where('date', $date)->exists()) {
                continue;
            }

            $availableKeyboardists = $this->getAvailableUsers($keyboardists, $date);
            $availableSongLeaders = $this->getAvailableUsers($songLeaders, $date);

            if (count($availableKeyboardists) >= 3 && count($availableSongLeaders) >= 6) {
                $confirmationDeadline = Carbon::parse($date)->subDays(3);
                $schedule = $this->initializeSchedule($sessions, $availableKeyboardists, $availableSongLeaders, $previousAssignments, $date, $assignmentCount);

                foreach ($schedule as $session => $assignedUsers) {
                    $jadwal = JadwalPelayanan::create([
                        'date' => $date,
                        'jadwal' => $session,
                        'id_pemusik' => $assignedUsers['keyboardist']->id,
                        'id_sl1' => $assignedUsers['song_leaders'][0]->id,
                        'id_sl2' => $assignedUsers['song_leaders'][1]->id,
                        'status' => 0,
                        'confirmation_deadline' => $confirmationDeadline,
                    ]);

                    $assignedUsers['keyboardist']->notify(new NotifikasiJadwalBaru($jadwal));
                    $assignedUsers['song_leaders'][0]->notify(new NotifikasiJadwalBaru($jadwal));
                    $assignedUsers['song_leaders'][1]->notify(new NotifikasiJadwalBaru($jadwal));

                    $previousAssignments['keyboardists'][] = $assignedUsers['keyboardist']->id;
                    $previousAssignments['songLeaders'][] = $assignedUsers['song_leaders'][0]->id;
                    $previousAssignments['songLeaders'][] = $assignedUsers['song_leaders'][1]->id;

                    $assignmentCount[$assignedUsers['keyboardist']->id] = ($assignmentCount[$assignedUsers['keyboardist']->id] ?? 0) + 1;
                    $assignmentCount[$assignedUsers['song_leaders'][0]->id] = ($assignmentCount[$assignedUsers['song_leaders'][0]->id] ?? 0) + 1;
                    $assignmentCount[$assignedUsers['song_leaders'][1]->id] = ($assignmentCount[$assignedUsers['song_leaders'][1]->id] ?? 0) + 1;
                }
            }
        }
    }

    // Fungsi untuk memilih pengguna yang tersedia berdasarkan tanggal
    private function getAvailableUsers($users, $date)
    {
        return $users->filter(function ($user) use ($date) {
            return $user->availabilities->contains('date', $date);
        });
    }

    // Fungsi untuk menginisialisasi jadwal dan melacak pengguna yang telah ditugaskan dengan fitness
    private function initializeSchedule($sessions, $availableKeyboardists, $availableSongLeaders, $previousAssignments, $date, &$assignmentCount)
    {
        $schedule = [];
        $usedUsers = [];

        foreach ($sessions as $session) {
            // Pilih keyboardist dan song leaders untuk sesi ini dengan memperhitungkan fitness
            $keyboardist = $this->getRandomAvailableUser($availableKeyboardists, $usedUsers, $assignmentCount);

            // Pilih Song Leader 1 yang berbeda dengan SL2 nanti
            $songLeader1 = $this->getRandomAvailableUser($availableSongLeaders, $usedUsers, $assignmentCount);

            // Pilih Song Leader 2 yang berbeda dengan Song Leader 1
            $songLeader2 = $this->getRandomAvailableUser($availableSongLeaders, $usedUsers, $assignmentCount, $songLeader1);

            // Jika Song Leader 1 dan Song Leader 2 tetap sama, pilih ulang SL2
            if ($songLeader1->id === $songLeader2->id) {
                $songLeader2 = $this->getRandomAvailableUser($availableSongLeaders, $usedUsers, $assignmentCount, $songLeader1);
            }

            // Simpan jadwal
            $schedule[$session] = [
                'keyboardist' => $keyboardist,
                'song_leaders' => [$songLeader1, $songLeader2],
            ];

            // Tambahkan pengguna yang sudah dipilih ke dalam daftar usedUsers
            $usedUsers = array_merge($usedUsers, [$keyboardist->id], [$songLeader1->id, $songLeader2->id]);
        }

        return $schedule;
    }

    // Fungsi untuk memilih pengguna yang belum ditugaskan berturut-turut, dengan mempertimbangkan fitness
    private function getRandomAvailableUser($availableUsers, $usedUsers, &$assignmentCount, $exclude = null)
    {
        // Filter pengguna yang sudah ditugaskan pada minggu sebelumnya
        $filteredUsers = $availableUsers->filter(function ($user) use ($usedUsers) {
            return !in_array($user->id, $usedUsers);
        });

        // Jika ada pengguna yang tersisa setelah filter, pilih salah satunya
        if ($filteredUsers->count() > 0) {
            // Pilih pengguna yang memiliki tugas paling sedikit, untuk distribusi yang lebih merata
            $filteredUsers = $filteredUsers->sortBy(function ($user) use ($assignmentCount) {
                return $assignmentCount[$user->id] ?? 0; // Pengguna yang ditugaskan lebih sedikit memiliki nilai fitness lebih tinggi
            });

            // Jika ada pengecualian (untuk Song Leader 2), kita pastikan pengguna yang dipilih berbeda
            if ($exclude) {
                $filteredUsers = $filteredUsers->where('id', '!=', $exclude->id);
            }

            return $filteredUsers->first();
        }

        // Jika tidak ada yang tersisa, fallback ke memilih pengguna yang sudah ditugaskan berturut-turut
        return $availableUsers->random();
    }

    public function confirmOverdueSchedules()
    {
        // Ambil semua jadwal yang belum dikonfirmasi dan sudah melewati deadline
        $overdueSchedules = JadwalPelayanan::where('is_confirmed', 0)
            ->where('confirmation_deadline', '<', Carbon::now())
            ->get();

        foreach ($overdueSchedules as $jadwal) {
            // Update status menjadi dikonfirmasi
            $jadwal->update([
                'status_pemusik' => 1,
                'status_sl1' => 1,
                'status_sl2' => 1,
                'is_confirmed' => 1,
                'is_locked' => true, // Kunci jadwal setelah semua mengonfirmasi
            ]);

            // Pindahkan ke history jika diperlukan
            HistoryJadwalPelayanan::create([
                'jadwal_pelayanan_id' => $jadwal->id,
                'date' => $jadwal->date,
                'jadwal' => $jadwal->jadwal,
                'id_pemusik' => $jadwal->id_pemusik,
                'id_sl1' => $jadwal->id_sl1,
                'id_sl2' => $jadwal->id_sl2,
                'is_confirmed' => 1,
                'is_locked' => 1,
            ]);
        }
    }

    public function checkScheduleForCurrentMonth()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $scheduleExists = JadwalPelayanan::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->exists();

        return response()->json(['exists' => $scheduleExists]);
    }

    public function generateNextMonthSchedule()
    {
        $nextMonth = Carbon::now()->addMonth()->month;
        $nextYear = Carbon::now()->addMonth()->year;

        // Fetch available dates for the next month
        $dates = Availability::distinct('date')
            ->whereMonth('date', $nextMonth)
            ->whereYear('date', $nextYear)
            ->pluck('date');

        // If no availability data, generate dates for the entire month
        if ($dates->isEmpty()) {
            $dates = collect(range(1, Carbon::now()->addMonth()->daysInMonth))->map(function ($day) use ($nextMonth, $nextYear) {
                return Carbon::create($nextYear, $nextMonth, $day)->toDateString();
            });
        }

        // Filter dates to include only Sundays
        $sundays = $dates->filter(function ($date) {
            return Carbon::parse($date)->isSunday();
        });

        // Initialize assignment count
        $assignmentCount = [];

        // Logic to generate schedule for each Sunday
        foreach ($sundays as $date) {
            // Check if a schedule already exists for this date
            $existingSchedules = JadwalPelayanan::where('date', $date)->exists();

            if ($existingSchedules) {
                continue; // Skip if a schedule already exists
            }

            // Fetch available users for the date
            $keyboardists = User::where('id_tugas', 1)->get();
            $songLeaders = User::where('id_tugas', 2)->get();

            $availableKeyboardists = $this->getAvailableUsers($keyboardists, $date);
            $availableSongLeaders = $this->getAvailableUsers($songLeaders, $date);

            // If not enough available users, fill with random users
            if (count($availableKeyboardists) < 3) {
                $additionalKeyboardists = $keyboardists->diff($availableKeyboardists)->random(3 - count($availableKeyboardists));
                $availableKeyboardists = $availableKeyboardists->merge($additionalKeyboardists);
            }

            if (count($availableSongLeaders) < 6) {
                $additionalSongLeaders = $songLeaders->diff($availableSongLeaders)->random(6 - count($availableSongLeaders));
                $availableSongLeaders = $availableSongLeaders->merge($additionalSongLeaders);
            }

            // Initialize and save the schedule
            $sessions = ['07:00', '10:00', '18:00'];
            $schedule = $this->initializeSchedule($sessions, $availableKeyboardists, $availableSongLeaders, [], $date, $assignmentCount);

            foreach ($schedule as $session => $assignedUsers) {
                JadwalPelayanan::create([
                    'date' => $date,
                    'jadwal' => $session,
                    'id_pemusik' => $assignedUsers['keyboardist']->id,
                    'id_sl1' => $assignedUsers['song_leaders'][0]->id,
                    'id_sl2' => $assignedUsers['song_leaders'][1]->id,
                    'status' => 0, // Status "Menunggu"
                    'confirmation_deadline' => Carbon::parse($date)->subDays(3), // Set batas waktu konfirmasi
                ]);
            }
        }

        return redirect()->route('admin.jadwal-pelayanan')->with('success', 'Jadwal untuk bulan berikutnya berhasil dibuat.');
    }
}
