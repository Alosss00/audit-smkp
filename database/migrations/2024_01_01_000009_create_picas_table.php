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
        Schema::create('picas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_detail_id')->unique()->constrained('audit_details')->onDelete('cascade');
            $table->text('deskripsi_temuan');
            $table->text('akar_masalah')->nullable();
            $table->text('tindakan_koreksi')->nullable();
            $table->text('tindakan_pencegahan')->nullable();
            $table->date('tenggat_waktu')->nullable();
            $table->string('pic_perbaikan')->nullable();
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
            $table->text('catatan_verifikasi_auditor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('picas');
    }
};
