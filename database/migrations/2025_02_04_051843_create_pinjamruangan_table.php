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
        Schema::create('pinjamruangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('ruangan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('kegiatan');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->enum('status', ['Diajukan', 'Disetujui', 'Ditolak'])->default('Diajukan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjamruangan');
    }
};
