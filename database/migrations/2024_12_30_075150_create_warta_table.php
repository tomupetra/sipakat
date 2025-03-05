<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tableName = 'warta';
    public function up(): void
    {
        Schema::create('warta', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->date('date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warta');
    }
};
