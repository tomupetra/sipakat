<?php

namespace App\Services;

use App\Models\User;
use App\Models\Availability;
use App\Models\JadwalPelayanan;
use Carbon\Carbon;

class ScheduleService
{
    public function generateSchedule()
    {
        $sessions = ['07:00', '10:00', '18:00']; // 3 sesi per hari Minggu
        $keyboardists = User::where('id_tugas', 1)->get();
        $songLeaders = User::where('id_tugas', 2)->get();

        $dates = Availability::distinct('date')->pluck('date');

        // Menyimpan riwayat penugasan pengguna untuk menghindari penugasan berturut-turut
        $previousAssignments = [
            'keyboardists' => [],
            'songLeaders' => [],
        ];

        // Penyimpanan distribusi tugas untuk fitness
        $assignmentCount = [];

        foreach ($dates as $date) {
            $availableKeyboardists = $this->getAvailableUsers($keyboardists, $date);
            $availableSongLeaders = $this->getAvailableUsers($songLeaders, $date);

            // Pastikan ada cukup keyboardist dan song leader untuk 3 sesi
            if (count($availableKeyboardists) >= 3 && count($availableSongLeaders) >= 6) {
                // Menghitung batas waktu konfirmasi (misalnya, 3 hari sebelum tanggal pelayanan)
                $confirmationDeadline = Carbon::parse($date)->subDays(3); // 3 hari sebelum tanggal pelayanan

                // Inisialisasi jadwal untuk minggu ini dengan fitness
                $schedule = $this->initializeSchedule($sessions, $availableKeyboardists, $availableSongLeaders, $previousAssignments, $date, $assignmentCount);

                // Simpan jadwal ke database
                foreach ($schedule as $session => $assignedUsers) {
                    JadwalPelayanan::create([
                        'date' => $date,
                        'jadwal' => $session,
                        'id_pemusik' => $assignedUsers['keyboardist']->id,
                        'id_sl1' => $assignedUsers['song_leaders'][0]->id,
                        'id_sl2' => $assignedUsers['song_leaders'][1]->id,
                        'status' => 0, // Status "Menunggu"
                        'confirmation_deadline' => $confirmationDeadline, // Set batas waktu konfirmasi
                    ]);

                    // Update riwayat penugasan
                    $previousAssignments['keyboardists'][] = $assignedUsers['keyboardist']->id;
                    $previousAssignments['songLeaders'][] = $assignedUsers['song_leaders'][0]->id;
                    $previousAssignments['songLeaders'][] = $assignedUsers['song_leaders'][1]->id;

                    // Update distribusi tugas
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
}
