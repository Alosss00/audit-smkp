<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_daftar_hadirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_sesi_id')->constrained('audit_sesis')->cascadeOnDelete();
            $table->enum('jenis', ['pembukaan', 'penutupan']);
            $table->date('tanggal')->nullable();
            $table->string('lokasi', 200)->nullable();
            $table->string('judul_kegiatan', 200)->nullable();
            $table->string('scan_path', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['audit_sesi_id', 'jenis']);
        });

        Schema::create('audit_daftar_hadir_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_daftar_hadir_id')->constrained('audit_daftar_hadirs')->cascadeOnDelete();
            $table->string('nama', 150);
            $table->string('jabatan', 150)->nullable();
            $table->string('unit_instansi', 150)->nullable();
            $table->enum('peran', ['auditor', 'auditi', 'pemandu', 'tamu'])->default('auditi');
            $table->boolean('hadir')->default(true);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['audit_daftar_hadir_id', 'urutan']);
        });

        Schema::create('audit_respon_manajemens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_sesi_id')->constrained('audit_sesis')->cascadeOnDelete();
            $table->date('tanggal_respon')->nullable();
            $table->string('nama_penandatangan', 150)->nullable();
            $table->string('jabatan_penandatangan', 150)->nullable();
            $table->longText('isi_respon')->nullable();
            $table->string('dokumen_path', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('audit_sesi_id');
        });

        Schema::create('audit_laporan_iujps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_laporan_id')->constrained('audit_laporans')->cascadeOnDelete();
            $table->foreignId('sumber_audit_sesi_id')->nullable()->constrained('audit_sesis')->nullOnDelete();
            $table->string('nama_perusahaan_iujp', 200);
            $table->string('nomor_iujp', 150)->nullable();
            $table->string('bidang_usaha', 200)->nullable();
            $table->date('tanggal_audit')->nullable();
            $table->decimal('skor_pencapaian', 5, 2)->nullable();
            $table->unsignedInteger('jumlah_temuan_kritikal')->default(0);
            $table->unsignedInteger('jumlah_temuan_mayor')->default(0);
            $table->unsignedInteger('jumlah_temuan_minor')->default(0);
            $table->text('ringkasan')->nullable();
            $table->string('dokumen_path', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('audit_laporan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_laporan_iujps');
        Schema::dropIfExists('audit_respon_manajemens');
        Schema::dropIfExists('audit_daftar_hadir_pesertas');
        Schema::dropIfExists('audit_daftar_hadirs');
    }
};
