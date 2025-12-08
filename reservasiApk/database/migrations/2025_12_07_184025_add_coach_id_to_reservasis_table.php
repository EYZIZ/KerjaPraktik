<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservasis', function (Blueprint $table) {

            // Tambah kolom UUID coach_id
            $table->uuid('coach_id')->nullable()->after('lapangan_id');

            // Tambah foreign key ke tabel coaches
            $table->foreign('coach_id')
                ->references('id')
                ->on('coaches')
                ->nullOnDelete(); // jika coach dihapus → coach_id otomatis null
        });
    }

    public function down(): void
    {
        Schema::table('reservasis', function (Blueprint $table) {
            $table->dropForeign(['coach_id']);
            $table->dropColumn('coach_id');
        });
    }
};
