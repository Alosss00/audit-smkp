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
        Schema::create('restore_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('kode')->unique(); // e.g. RP-20260915-114500
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('tipe')->default('manual'); // 'manual', 'auto_prerollback', 'auto_finalisasi', 'auto_import'
            $table->string('file_path');
            $table->string('file_size')->default('0 KB');
            $table->json('table_counts')->nullable(); // Snapshot row counts per table
            $table->string('checksum')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restore_points');
    }
};
