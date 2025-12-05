<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservasi_slots', function (Blueprint $table) {
            $table->id();
            $table->uuid('reservasi_id');
            $table->uuid('lapangan_id');
            $table->date('tanggal');
            $table->time('jam_mulai');   // contoh: 07:00
            $table->time('jam_selesai'); // contoh: 08:00
            $table->timestamps();

            $table->foreign('reservasi_id')
                ->references('id')->on('reservasis')
                ->cascadeOnDelete();

            $table->foreign('lapangan_id')
                ->references('id')->on('lapangans')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi_slots');
    }
};
