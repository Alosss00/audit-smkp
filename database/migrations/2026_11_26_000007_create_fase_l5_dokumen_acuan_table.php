<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_dokumen_acuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_sesi_id')->constrained('audit_sesis')->cascadeOnDelete();
            $table->enum('jenis_dokumen', ['rkab', 'sop_audit', 'manual_smkp', 'dokumen_lainnya'])
                ->default('dokumen_lainnya');
            $table->string('judul', 200);
            $table->string('nomor_dokumen', 100)->nullable();
            $table->date('tanggal_dokumen')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('diunggah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['audit_sesi_id', 'jenis_dokumen']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_dokumen_acuans');
    }
};
