<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwal_pelayanan', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('jadwal');
            $table->foreignId('id_pemusik')->constrained('users')->onDelete('cascade'); // Foreign key ke tabel users
            $table->foreignId('id_sl1')->constrained('users')->onDelete('cascade'); // Foreign key ke tabel users
            $table->foreignId('id_sl2')->constrained('users')->onDelete('cascade'); // Foreign key ke tabel users
            $table->tinyInteger('status_pemusik')->default(0); // 0: Menunggu, 1: Diterima, 2: Ditolak
            $table->tinyInteger('status_sl1')->default(0); // 0: Menunggu, 1: Diterima, 2: Ditolak
            $table->tinyInteger('status_sl2')->default(0); // 0: Menunggu, 1: Diterima, 2: Ditolak
            $table->timestamp('confirmation_deadline')->nullable(); // Batas waktu konfirmasi
            $table->tinyInteger('is_confirmed')->default(0); // 0: Menunggu, 1: Diterima, 2: Ditolak
            $table->boolean('is_locked')->default(false); // Status konfirmasi pemusik
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelayanan');
    }
};
