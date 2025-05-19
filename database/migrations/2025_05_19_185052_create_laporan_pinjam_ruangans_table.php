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
        Schema::create('laporan_pinjam_ruangan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_peminjam');
            $table->string('ruangan');
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->string('keterangan')->nullable();
            $table->string('status')->default('Disetujui');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pinjam_ruangans');
    }
};
