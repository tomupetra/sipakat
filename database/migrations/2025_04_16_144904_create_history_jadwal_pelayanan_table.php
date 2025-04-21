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
        Schema::create('history_jadwal_pelayanan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_pelayanan_id');
            $table->foreign('jadwal_pelayanan_id')->references('id')->on('jadwal_pelayanan');
            $table->date('date');
            $table->time('jadwal');
            $table->unsignedBigInteger('id_pemusik');
            $table->unsignedBigInteger('id_sl1');
            $table->unsignedBigInteger('id_sl2');
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
        Schema::dropIfExists('history_jadwal_pelayanan');
    }
};
