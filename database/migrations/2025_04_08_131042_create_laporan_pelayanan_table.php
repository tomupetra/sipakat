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
        Schema::create('laporan_pelayanan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('sesi');
            $table->string('pemusik');
            $table->string('sl1');
            $table->string('sl2');
            $table->boolean('is_confirmed');
            $table->boolean('is_locked');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pelayanan');
    }
};
