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
        Schema::create('reservasis', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // users.id = BIGINT auto increment
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // lapangans.id = UUID
            $table->foreignUuid('lapangan_id')
                ->constrained('lapangans')
                ->cascadeOnDelete();

            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->integer('durasi')->default(1);
            $table->time('jam_selesai');

            $table->integer('total_harga')->default(0);

            $table->enum('status', ['pending', 'disetujui', 'dibatalkan'])->default('pending');

            // --- Payment Gateway ---
            $table->enum('payment_status', ['unpaid', 'paid', 'failed'])->default('unpaid');
            $table->string('payment_method')->nullable();
            $table->string('payment_transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservasis');
    }
};
