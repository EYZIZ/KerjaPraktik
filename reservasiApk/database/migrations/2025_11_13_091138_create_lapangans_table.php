<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('lapangans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('photo')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->integer('price_per_hour')->default(0);
            $table->enum('status', ['Tersedia', 'Perbaikan','Terisi'])->default('Tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lapangans');
    }
};
