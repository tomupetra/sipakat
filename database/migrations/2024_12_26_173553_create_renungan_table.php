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
        Schema::create('renungan', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->longText('ayat_harian');
            $table->string('bacaan_pagi');
            $table->string('bacaan_malam');
            $table->string('lagu_ende');
            $table->string('title');
            $table->longText('content');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renungan');
    }
};
