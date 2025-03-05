<?php

namespace App\Services;

use App\Models\Availability;
use App\Models\User;
use Illuminate\Support\Facades\Log; // Import Log

class GeneticAlgorithm
{
    protected $availableMusicians;
    protected $availableSongLeaders;
    protected $populationSize;
    protected $generations;
    protected $crossoverRate;
    protected $mutationRate;
    protected $startDate;
    protected $endDate;
    protected $services;

    // Method initialize untuk menyimpan parameter
    public function initialize(
        $availableMusicians,
        $availableSongLeaders,
        $populationSize,
        $generations,
        $crossoverRate,
        $mutationRate,
        $startDate,
        $endDate,
        $services
    ) {
        $this->availableMusicians = $availableMusicians;
        $this->availableSongLeaders = $availableSongLeaders;
        $this->populationSize = $populationSize;
        $this->generations = $generations;
        $this->crossoverRate = $crossoverRate;
        $this->mutationRate = $mutationRate;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->services = $services;
    }

    public function run()
    {
        // 1. Inisialisasi populasi
        $population = $this->initializePopulation();

        // 2. Loop hingga kriteria berhenti terpenuhi
        for ($generation = 0; $generation < $this->generations; $generation++) {
            // a. Evaluasi fitness
            $this->evaluatePopulation($population);

            // b. Seleksi
            $selectedParents = $this->selection($population);

            // c. Crossover
            $offspring = $this->crossover($selectedParents);


            // d. Mutasi
            $this->mutation($offspring);

            // e. Penggantian populasi
            $population = $this->replacePopulation($population, $offspring);


            // f. Cek konvergensi (opsional)
            if ($this->hasConverged($population)) {
                break;
            }
        }

        // 3. Kembalikan individu terbaik (jadwal terbaik)
        usort($population, function ($a, $b) {
            return $this->calculateFitness($b) <=> $this->calculateFitness($a);
        }); // Urutkan descending berdasarkan fitness

        return $population[0]; // Jadwal terbaik
    }


    private function initializePopulation()
    {
        $population = [];
        for ($i = 0; $i < $this->populationSize; $i++) {
            $population[] = $this->generateRandomSchedule();
        }
        return $population;
    }



    private function generateRandomSchedule()
    {
        $schedule = [];

        // Pastikan ada tanggal yang tersedia
        if (empty($this->availableMusicians) && empty($this->availableSongLeaders)) {

            Log::info('Tidak ada Musisi atau SongLeaders yang tersedia.');
            return []; // Atau throw exception, tergantung kebutuhan
        }

        // Buat jadwal untuk setiap tanggal dalam rentang
        $currentDate = \Carbon\Carbon::parse($this->startDate);
        $endDate = \Carbon\Carbon::parse($this->endDate);

        while ($currentDate <= $endDate) {

            $dateString = $currentDate->format('Y-m-d');

            foreach ($this->services as $service) {

                $musician = null;
                $songLeader1 = null;
                $songLeader2 = null;

                // Cek apakah ada musisi yang tersedia pada tanggal ini
                if (isset($this->availableMusicians[$dateString]) && !empty($this->availableMusicians[$dateString])) {
                    $musicianKey = array_rand($this->availableMusicians[$dateString]);
                    $musician = $this->availableMusicians[$dateString][$musicianKey];
                }

                // Cek apakah ada song leader yang tersedia pada tanggal ini
                if (isset($this->availableSongLeaders[$dateString]) && !empty($this->availableSongLeaders[$dateString])) {
                    // Pilih 2 song leader secara acak (pastikan tidak sama)
                    $availableSLs = $this->availableSongLeaders[$dateString];
                    if (count($availableSLs) >= 2) {
                        $songLeaderKeys = array_rand($availableSLs, 2);
                        $songLeader1 = $availableSLs[$songLeaderKeys[0]];
                        $songLeader2 = $availableSLs[$songLeaderKeys[1]];
                    } elseif (count($availableSLs) == 1) {
                        $songLeader1  = $availableSLs[array_rand($availableSLs)];
                        $songLeader2 = null; //Tidak Ada Cukup Song Leader
                    }
                }

                // Jika ada pemusik dan song leader yang tersedia, tambahkan ke jadwal
                if ($musician !== null && $songLeader1 !== null) {
                    $schedule["$dateString - Ibadah $service"] = [
                        'Pemusik' => $musician,
                        'Song Leader 1' => $songLeader1,
                        'Song Leader 2' => $songLeader2,
                    ];
                } else {
                    // Log jika tidak ada cukup personil
                    Log::info('Tidak cukup personil untuk tanggal dan layanan ini.', [
                        'tanggal' => $dateString,
                        'layanan' => $service,
                        'musisi' => $musician,
                        'song_leader_1' => $songLeader1,
                        'song_leader_2' => $songLeader2
                    ]);
                }
            }
            $currentDate->addDay(); // Lanjut ke hari berikutnya
        }
        return $schedule;
    }



    private function evaluatePopulation(&$population)
    {

        foreach ($population as &$individual) {
            $individual['fitness'] = $this->calculateFitness($individual);
        }
    }



    //  Fungsi fitness
    private function calculateFitness($schedule)
    {

        $fitness = 0;
        $assigned = []; // Array untuk melacak tugas yang sudah diberikan pada hari yang sama.

        // Cek apakah schedule kosong
        if (empty($schedule)) {
            return 0; // Fitness 0 jika jadwal kosong
        }


        foreach ($schedule as $dateService => $personnel) {
            list($date, $service) = explode(' - ', $dateService, 2);

            // Periksa duplikasi dalam satu hari
            foreach (['Pemusik', 'Song Leader 1', 'Song Leader 2'] as $role) {
                if (isset($personnel[$role])) {
                    $personId = $personnel[$role];
                    if (!isset($assigned[$date])) {
                        $assigned[$date] = [];
                    }
                    if (in_array($personId, $assigned[$date])) {
                        // Penalty jika orang yang sama ditugaskan lebih dari sekali dalam sehari
                        $fitness -= 30;
                    } else {
                        $assigned[$date][] = $personId;
                    }
                }
            }

            $fitness += 20; // Tambah fitness jika penjadwalan berhasil
        }


        return $fitness;
    }


    private function selection($population)
    {
        //  Roulette Wheel Selection + Elitism

        $elitismCount = max(1, round(0.1 * count($population))); //minimal 1, 10% dari total populasi.
        $newPopulation = [];

        // Elitism: Simpan individu terbaik langsung
        usort($population, function ($a, $b) {
            return $this->calculateFitness($b) <=> $this->calculateFitness($a);
        });
        for ($i = 0; $i < $elitismCount; $i++) {
            $newPopulation[] = $population[$i];
        }

        // Hitung total fitness
        $fitnessSum = 0;

        foreach ($population as $individual) {
            $fitnessSum += $individual['fitness'];
        }


        // Roulette wheel selection
        while (count($newPopulation) < count($population)) {
            $randomValue = mt_rand() / mt_getrandmax(); // Angka acak antara 0 dan 1

            //Jika fitnessSum <= 0, beri peluang yang sama untuk semua individu
            if ($fitnessSum <= 0) {
                $probabilityThreshold = 1 / count($population);
            } else {
                $probabilityThreshold = 0;
                for ($i = 0; $i < count($population); $i++) {
                    $probabilityThreshold += ($population[$i]['fitness'] / $fitnessSum); // Menghitung probability
                    if ($randomValue <= $probabilityThreshold) {
                        $newPopulation[] = $population[$i]; //Memilih Individu
                        break; //Keluar dari loop, setelah individu dipilih
                    }
                }
            }
        }

        return $newPopulation;
    }



    private function crossover($population)
    {
        // Elitism: Lewati individu terbaik
        $elitismCount = max(1, round(0.1 * count($population))); // 10% populasi, minimal 1.
        $offspring = [];

        // Salin individu elit ke offspring (tidak di-crossover)
        for ($i = 0; $i < $elitismCount; $i++) {
            $offspring[] = $population[$i];
        }

        // One-point crossover untuk sisa populasi
        while (count($offspring) < count($population)) {
            // Pilih 2 parent secara acak (pastikan tidak sama)
            if (count($population) < 2) {
                //Tidak Cukup parent untuk melakukan crossover
                break;
            }

            //Pilih dua induk acak yang berbeda
            do {
                $parent1Index = array_rand($population);
                $parent2Index = array_rand($population);
            } while ($parent1Index === $parent2Index);  //Pastikan index parent tidak sama

            $parent1 = $population[$parent1Index];
            $parent2 = $population[$parent2Index];

            // Lakukan crossover jika memenuhi crossover rate
            if ((mt_rand() / mt_getrandmax()) < $this->crossoverRate) {

                // One-point crossover
                $keys1 = array_keys($parent1);
                $keys2 = array_keys($parent2);

                // Hapus kunci 'fitness' jika ada, agar tidak ikut di-crossover
                $keys1 = array_diff($keys1, ['fitness']);
                $keys2 = array_diff($keys2, ['fitness']);

                if (!empty($keys1) && !empty($keys2)) { // Cek array key kosong atau tidak
                    $crossoverPoint = mt_rand(0, min(count($keys1), count($keys2)) - 1);


                    $child1 = [];
                    $child2 = [];


                    // Ambil data dari parent1 sampai titik crossover
                    for ($i = 0; $i <= $crossoverPoint; $i++) {
                        if (isset($keys1[$i])) {
                            $child1[$keys1[$i]] = $parent1[$keys1[$i]];
                        }
                    }

                    // Ambil data dari parent2 setelah titik crossover, jika tidak ada (Prioritas Parent 1)
                    for ($i = $crossoverPoint + 1; $i < count($keys2); $i++) {

                        //Periksa apakah kunci dari parent2 sudah ada pada child1
                        if (isset($keys2[$i]) && !isset($child1[$keys2[$i]])) {
                            $child1[$keys2[$i]] = $parent2[$keys2[$i]];
                        }
                    }

                    // Ambil data dari parent2 sampai titik crossover
                    for ($i = 0; $i <= $crossoverPoint; $i++) {
                        if (isset($keys2[$i])) {
                            $child2[$keys2[$i]] = $parent2[$keys2[$i]];
                        }
                    }

                    // Ambil data dari parent1 setelah titik crossover, jika tidak ada (Prioritas Parent 2)
                    for ($i = $crossoverPoint + 1; $i < count($keys1); $i++) {

                        if (isset($keys1[$i]) && !isset($child2[$keys1[$i]])) {
                            $child2[$keys1[$i]] = $parent1[$keys1[$i]];
                        }
                    }

                    $offspring[] = $child1;
                    if (count($offspring) < count($population)) {
                        $offspring[] = $child2;
                    }
                } else {
                    //Tidak dapat melakukan crossover karena salah satu orang tua tidak memiliki jadwal
                    $offspring[] = $parent1;
                    $offspring[] = $parent2;
                }
            } else {
                // Jika tidak crossover, masukkan parent ke offspring
                $offspring[] = $parent1;
                if (count($offspring) < count($population)) {
                    $offspring[] = $parent2;
                }
            }
        }
        return $offspring;
    }



    private function mutation(&$population)
    {

        foreach ($population as &$child) {
            //Lewati jika child adalah array kosong
            if (empty($child)) {
                continue;
            }

            if ((mt_rand() / mt_getrandmax()) < $this->mutationRate) {
                //  Swap mutation (tukar 2 jadwal acak)
                $keys = array_keys($child);
                // Hapus kunci 'fitness' jika ada, agar tidak ikut di-mutasi
                $keys = array_diff($keys, ['fitness']);

                if (count($keys) > 1) { //Memastikan ada cukup jadwal untuk swap
                    $mutationPoint1 = array_rand($keys);
                    do {
                        $mutationPoint2 = array_rand($keys);
                    } while ($mutationPoint1 == $mutationPoint2);  //Pastikan 2 index tidak sama

                    // Swap jadwal
                    $temp = $child[$keys[$mutationPoint1]];
                    $child[$keys[$mutationPoint1]] = $child[$keys[$mutationPoint2]];
                    $child[$keys[$mutationPoint2]] = $temp;
                }

                //  Random resetting (ganti petugas dengan yang available, acak)
                $randomKey = array_rand($child); // Pilih jadwal secara acak
                list($date, $service) = explode(' - ', $randomKey, 2);
                $date = trim($date);
                $service = trim($service);

                // Ambil petugas yang available di tanggal dan service tersebut (Pemusik)
                if (isset($this->availableMusicians[$date]) && !empty($this->availableMusicians[$date])) {

                    $musicianKey = array_rand($this->availableMusicians[$date]);
                    $newMusician = $this->availableMusicians[$date][$musicianKey];
                    $child[$randomKey]['Pemusik'] = $newMusician;
                }

                // Ambil petugas yang available di tanggal dan service tersebut (Song Leader)
                if (isset($this->availableSongLeaders[$date]) && !empty($this->availableSongLeaders[$date])) {
                    $availableSLs = $this->availableSongLeaders[$date];
                    if (count($availableSLs) >= 2) {
                        $songLeaderKeys = array_rand($availableSLs, 2);
                        $child[$randomKey]['Song Leader 1'] = $availableSLs[$songLeaderKeys[0]];
                        $child[$randomKey]['Song Leader 2'] = $availableSLs[$songLeaderKeys[1]];
                    } elseif (count($availableSLs) == 1) {
                        $child[$randomKey]['Song Leader 1']  = $availableSLs[array_rand($availableSLs)];
                        $child[$randomKey]['Song Leader 2'] = null; //Tidak Ada Cukup Song Leader
                    }
                }
            }
        }
    }

    private function replacePopulation($population, $offspring)
    {
        // Implementasi penggantian populasi (misalnya, generational replacement)
        // Generational replacement: Ganti seluruh populasi dengan offspring
        return $offspring;
    }


    private function hasConverged($population)
    {
        // Implementasi pengecekan konvergensi (opsional)
        // Cek konvergensi sederhana: Jika semua individu memiliki fitness yang sama
        if (empty($population)) {
            return false; // Populasi kosong, belum konvergen
        }

        $firstFitness = $population[0]['fitness'];
        foreach ($population as $individual) {
            if ($individual['fitness'] != $firstFitness) {
                return false; // Ada yang berbeda, belum konvergen
            }
        }

        return true; // Semua sama, dianggap konvergen
    }
}