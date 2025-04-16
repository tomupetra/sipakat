<?php

namespace App\Services;

use App\Models\User;
use App\Models\Availability;
use App\Models\JadwalPelayanan;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GeneticAlgorithm
{
    private $keyboardists;
    private $songLeaders;
    private $weeks;
    private $sessions = ['07:00', '10:00', '18:00']; // 3 sesi per hari Minggu
    private $populationSize = 30;
    private $generations = 100;

    public function __construct()
    {
        // Ambil data keyboardist dan song leader dari database
        $this->keyboardists = User::where('id_tugas', 1)->get();
        $this->songLeaders = User::where('id_tugas', 2)->get();

        // Menghitung jumlah minggu dalam bulan
        $firstSundayOfMonth = Carbon::now()->startOfMonth()->next(Carbon::SUNDAY);
        $this->weeks = $firstSundayOfMonth->copy()->endOfMonth()->diffInWeeks($firstSundayOfMonth) + 1;
    }

    public function optimizeSchedule()
    {
        $this->validateAvailabilities(); // Validasi data availability sebelum menjalankan algoritma

        if ($this->keyboardists->count() < 3 || $this->songLeaders->count() < 6) {
            Log::warning('Not enough keyboardists or song leaders available.');
            return [];  // Menghentikan proses jika data tidak mencukupi
        }

        $population = $this->initializePopulation();

        for ($generation = 0; $generation < $this->generations; $generation++) {
            $fitnessScores = $this->calculateFitnessScores($population);
            $parents = $this->selectParents($population, $fitnessScores);
            $population = $this->createNewGeneration($parents);
        }

        return $this->getBestSchedule($population);
    }

    private function validateAvailabilities()
    {
        $firstSundayOfMonth = Carbon::now()->startOfMonth()->next(Carbon::SUNDAY);
        $weeks = $firstSundayOfMonth->copy()->endOfMonth()->diffInWeeks($firstSundayOfMonth) + 1;

        foreach ($this->keyboardists as $keyboardist) {
            foreach (range(1, $weeks) as $week) {
                $targetDate = $firstSundayOfMonth->copy()->addWeeks($week - 1);
                if (!$this->isAvailable($keyboardist->id, $week)) {
                    Log::warning("Keyboardist {$keyboardist->id} tidak tersedia pada minggu ke-$week ($targetDate).");
                }
            }
        }

        foreach ($this->songLeaders as $songLeader) {
            foreach (range(1, $weeks) as $week) {
                $targetDate = $firstSundayOfMonth->copy()->addWeeks($week - 1);
                if (!$this->isAvailable($songLeader->id, $week)) {
                    Log::warning("Song Leader {$songLeader->id} tidak tersedia pada minggu ke-$week ($targetDate).");
                }
            }
        }
    }

    private function initializePopulation()
    {
        $population = [];
        for ($i = 0; $i < $this->populationSize; $i++) {
            $schedule = [];
            $usedUsers = [];  // Untuk melacak user yang sudah ditugaskan dalam satu hari
            for ($week = 1; $week <= $this->weeks; $week++) {
                foreach ($this->sessions as $session) {
                    // Pilih keyboardist dan song leaders berdasarkan availability
                    $keyboardist = $this->getRandomAvailableKeyboardist($week, $usedUsers);
                    $songLeaders = $this->getRandomAvailableSongLeaders($week, $usedUsers);

                    // Jika tidak ada yang tersedia, fallback dengan pengguna yang sudah bertugas
                    if (!$keyboardist || !$songLeaders) {
                        $keyboardist = $this->getFallbackKeyboardist($week, $usedUsers);
                        $songLeaders = $this->getFallbackSongLeaders($week, $usedUsers);
                    }

                    // Jika tetap tidak ada, assign satu user ke lebih dari satu sesi (opsi terakhir)
                    if (!$keyboardist || !$songLeaders) {
                        $keyboardist = $this->getAnyKeyboardist($usedUsers);
                        $songLeaders = $this->getAnySongLeaders($usedUsers);
                    }

                    if ($keyboardist && $songLeaders) {
                        $schedule[$week][$session] = [
                            'keyboardist' => $keyboardist,
                            'song_leaders' => $songLeaders,
                        ];

                        // Tambahkan user yang sudah dipilih ke daftar usedUsers
                        $usedUsers = array_merge($usedUsers, [$keyboardist->id], $songLeaders->pluck('id')->toArray());
                    }
                }
            }
            $population[] = $schedule;
        }

        return $population;
    }

    private function isAvailable($userId, $week)
    {
        // Ambil tanggal Minggu pertama bulan ini
        $firstSundayOfMonth = Carbon::now()->startOfMonth()->next(Carbon::SUNDAY);

        // Hitung tanggal untuk minggu yang diminta
        $targetDate = $firstSundayOfMonth->copy()->addWeeks($week - 1);

        // Cek apakah user memiliki availability pada tanggal tersebut
        return Availability::where('user_id', $userId)
            ->whereDate('date', $targetDate->toDateString())
            ->exists();
    }

    private function getRandomAvailableKeyboardist($week, $usedUsers)
    {
        $availableKeyboardists = $this->keyboardists->filter(function ($k) use ($week, $usedUsers) {
            return $this->isAvailable($k->id, $week) && !in_array($k->id, $usedUsers);
        });

        if ($availableKeyboardists->isEmpty()) {
            // Jika tidak ada yang tersedia, cari user yang memiliki tugas paling sedikit
            $fallbackKeyboardists = $this->keyboardists->filter(function ($k) use ($usedUsers) {
                return !in_array($k->id, $usedUsers);
            });

            if ($fallbackKeyboardists->isEmpty()) {
                return null;
            }

            return $fallbackKeyboardists->sortBy(function ($k) {
                return JadwalPelayanan::where('id_pemusik', $k->id)
                    ->orWhere('id_sl1', $k->id)
                    ->orWhere('id_sl2', $k->id)
                    ->count();
            })->first();
        }

        return $availableKeyboardists->random();
    }

    private function getRandomAvailableSongLeaders($week, $usedUsers)
    {
        $availableSongLeaders = $this->songLeaders->filter(function ($s) use ($week, $usedUsers) {
            return $this->isAvailable($s->id, $week) && !in_array($s->id, $usedUsers);
        });

        if ($availableSongLeaders->count() < 2) {
            // Jika tidak ada yang tersedia, cari user yang memiliki tugas paling sedikit
            $fallbackSongLeaders = $this->songLeaders->filter(function ($s) use ($usedUsers) {
                return !in_array($s->id, $usedUsers);
            });

            if ($fallbackSongLeaders->count() < 2) {
                return null;
            }

            return $fallbackSongLeaders->sortBy(function ($s) {
                return JadwalPelayanan::where('id_pemusik', $s->id)
                    ->orWhere('id_sl1', $s->id)
                    ->orWhere('id_sl2', $s->id)
                    ->count();
            })->take(2);
        }

        return $availableSongLeaders->random(2);
    }

    private function getFallbackKeyboardist($week, $usedUsers)
    {
        // Cari keyboardist yang belum ditugaskan pada hari ini dan bulan ini
        $fallbackKeyboardists = $this->keyboardists->filter(function ($k) use ($usedUsers) {
            return !in_array($k->id, $usedUsers) && !JadwalPelayanan::where('id_pemusik', $k->id)
                ->orWhere('id_sl1', $k->id)
                ->orWhere('id_sl2', $k->id)
                ->whereMonth('date', Carbon::now()->month)
                ->exists();
        });

        if ($fallbackKeyboardists->isEmpty()) {
            return null;
        }

        return $fallbackKeyboardists->random();
    }

    private function getFallbackSongLeaders($week, $usedUsers)
    {
        // Cari song leader yang belum ditugaskan pada hari ini dan bulan ini
        $fallbackSongLeaders = $this->songLeaders->filter(function ($s) use ($usedUsers) {
            return !in_array($s->id, $usedUsers) && !JadwalPelayanan::where('id_pemusik', $s->id)
                ->orWhere('id_sl1', $s->id)
                ->orWhere('id_sl2', $s->id)
                ->whereMonth('date', Carbon::now()->month)
                ->exists();
        });

        if ($fallbackSongLeaders->count() < 2) {
            return null;
        }

        return $fallbackSongLeaders->random(2);
    }

    private function getAnyKeyboardist($usedUsers)
    {
        return $this->keyboardists->filter(function ($k) use ($usedUsers) {
            return !in_array($k->id, $usedUsers);
        })->random();
    }

    private function getAnySongLeaders($usedUsers)
    {
        return $this->songLeaders->filter(function ($s) use ($usedUsers) {
            return !in_array($s->id, $usedUsers);
        })->random(2);
    }

    private function calculateFitnessScores($population)
    {
        return array_map([$this, 'calculateFitness'], $population);
    }

    private function calculateFitness($schedule)
    {
        $conflicts = 0;
        $assignments = [];
        $availabilityViolations = 0;

        foreach ($schedule as $week => $sessions) {
            $usedUsers = [];
            foreach ($sessions as $session => $assignees) {
                $keyboardist = $assignees['keyboardist']->id;
                $songLeaders = [$assignees['song_leaders'][0]->id, $assignees['song_leaders'][1]->id];

                // Cek konflik dalam satu hari
                if (in_array($keyboardist, $usedUsers) || array_intersect($songLeaders, $usedUsers)) {
                    $conflicts++;
                }
                $usedUsers = array_merge($usedUsers, [$keyboardist], $songLeaders);

                // Cek availability
                if (!$this->isAvailable($keyboardist, $week)) {
                    $availabilityViolations++;
                }
                foreach ($songLeaders as $leader) {
                    if (!$this->isAvailable($leader, $week)) {
                        $availabilityViolations++;
                    }
                }

                // Hitung jumlah tugas per pengguna
                $assignments[$keyboardist] = ($assignments[$keyboardist] ?? 0) + 1;
                foreach ($songLeaders as $leader) {
                    $assignments[$leader] = ($assignments[$leader] ?? 0) + 1;
                }
            }
        }

        // Hitung variance distribusi tugas
        $variance = $this->calculateVariance($assignments);

        // Fitness adalah kebalikan dari konflik, pelanggaran availability, dan variance
        return 1 / (1 + $conflicts + (5 * $availabilityViolations) + $variance); // Kurangi bobot availabilityViolations
    }

    private function calculateVariance($assignments)
    {
        if (empty($assignments)) {
            return 0;
        }

        $mean = array_sum($assignments) / count($assignments);
        $variance = 0;
        foreach ($assignments as $count) {
            $variance += pow($count - $mean, 2);
        }
        return $variance / count($assignments);
    }

    private function selectParents($population, $fitnessScores)
    {
        $totalFitness = array_sum($fitnessScores);

        // Jika total fitness adalah 0, berikan probabilitas yang sama untuk semua individu
        if ($totalFitness == 0) {
            return $population;
        }

        $probabilities = array_map(function ($score) use ($totalFitness) {
            return $score / $totalFitness;
        }, $fitnessScores);

        $parents = [];
        for ($i = 0; $i < $this->populationSize; $i++) {
            $rand = mt_rand() / mt_getrandmax();
            $cumulativeProbability = 0;
            foreach ($population as $index => $individual) {
                $cumulativeProbability += $probabilities[$index];
                if ($rand <= $cumulativeProbability) {
                    $parents[] = $individual;
                    break;
                }
            }
        }
        return $parents;
    }

    private function createNewGeneration($parents)
    {
        $newPopulation = [];
        for ($i = 0; $i < $this->populationSize; $i++) {
            $parent1 = $parents[array_rand($parents)];
            $parent2 = $parents[array_rand($parents)];
            $child = $this->crossover($parent1, $parent2);
            $newPopulation[] = $this->mutate($child);
        }
        return $newPopulation;
    }

    private function crossover($parent1, $parent2)
    {
        $child = [];
        foreach ($parent1 as $week => $sessions) {
            $child[$week] = (rand(0, 1) == 0) ? $sessions : $parent2[$week];
        }
        return $child;
    }

    private function mutate($schedule)
    {
        foreach ($schedule as $week => &$sessions) {
            foreach ($sessions as $session => &$assignees) {
                if (rand(0, 100) < 5) { // 5% mutation rate
                    $assignees['keyboardist'] = $this->getRandomAvailableKeyboardist($week, []);
                    $assignees['song_leaders'] = $this->getRandomAvailableSongLeaders($week, []);
                }
            }
        }
        return $schedule;
    }

    private function getBestSchedule($population)
    {
        $fitnessScores = $this->calculateFitnessScores($population);
        $bestIndex = array_search(max($fitnessScores), $fitnessScores);
        return $population[$bestIndex];
    }
}
