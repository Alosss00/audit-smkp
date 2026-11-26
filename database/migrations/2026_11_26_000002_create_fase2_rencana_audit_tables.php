<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('audit_rencanas')) {
            Schema::create('audit_rencanas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('audit_sesi_id')->unique()->constrained('audit_sesis')->cascadeOnDelete();
                $table->string('no_dokumen')->default('TT-MGT-FRS-026A');
                $table->string('jenis_audit')->nullable();
                $table->text('kriteria_audit')->nullable();
                $table->text('tujuan')->nullable();
                $table->text('ruang_lingkup')->nullable();
                $table->text('pengecualian')->nullable();
                $table->json('dokumen_relevan')->nullable();
                $table->text('fasilitas')->nullable();
                $table->text('distribusi_laporan')->nullable();
                $table->enum('kelompok_penandatangan', ['tambang', 'pengolahan_pemurnian', 'jasa_pertambangan'])->nullable();
                $table->enum('status', ['draft', 'final'])->default('draft');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('audit_rencana_tims')) {
            Schema::create('audit_rencana_tims', function (Blueprint $table) {
                $table->id();
                $table->foreignId('audit_rencana_id')->constrained('audit_rencanas')->cascadeOnDelete();
                $table->string('nama');
                $table->string('jabatan')->nullable();
                $table->enum('peran', ['lead_auditor', 'auditor', 'tenaga_ahli', 'pengamat'])->default('auditor');
                $table->unsignedSmallInteger('urutan')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('audit_rencana_jadwals')) {
            Schema::create('audit_rencana_jadwals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('audit_rencana_id')->constrained('audit_rencanas')->cascadeOnDelete();
                $table->unsignedSmallInteger('hari_ke');
                $table->date('tanggal');
                $table->time('jam_mulai');
                $table->time('jam_selesai');
                $table->enum('jenis_kegiatan', [
                    'rapat_pembukaan',
                    'audit_elemen',
                    'rapat_internal_tim',
                    'rapat_penutupan',
                    'lainnya',
                ])->default('audit_elemen');
                $table->text('kegiatan');
                $table->foreignId('elemen_id')->nullable()->constrained('elemens')->nullOnDelete();
                $table->string('auditor')->nullable();
                $table->string('auditi_pic')->nullable();
                $table->string('lokasi')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('audit_rencana_sampels')) {
            Schema::create('audit_rencana_sampels', function (Blueprint $table) {
                $table->id();
                $table->foreignId('audit_rencana_id')->constrained('audit_rencanas')->cascadeOnDelete();
                $table->foreignId('sub_elemen_id')->constrained('sub_elemens')->cascadeOnDelete();
                $table->enum('jenis_data', ['primer', 'sekunder'])->nullable();
                $table->json('metode')->nullable();
                $table->enum('teknik_sampling', [
                    'purposive_key_area',
                    'purposive_risk_based',
                    'purposive_accident_based',
                    'cluster',
                    'simple_random',
                    'accidental',
                    'stratified_random',
                    'professional_judgement',
                ])->nullable();
                $table->enum('metode_penentuan', [
                    'professional_judgement',
                    'slovin',
                    'best_practice',
                    'triangulasi',
                ])->nullable();
                $table->unsignedInteger('populasi_n')->nullable();
                $table->decimal('margin_error_e', 4, 3)->nullable();
                $table->unsignedInteger('jumlah_sampel')->nullable();
                $table->text('deskripsi_sampel')->nullable();
                $table->timestamps();

                $table->unique(['audit_rencana_id', 'sub_elemen_id']);
            });
        }

        if (! Schema::hasTable('master_penandatangan_rencana')) {
            Schema::create('master_penandatangan_rencana', function (Blueprint $table) {
                $table->id();
                $table->enum('kelompok', ['tambang', 'pengolahan_pemurnian', 'jasa_pertambangan']);
                $table->unsignedSmallInteger('urutan');
                $table->string('jabatan');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['kelompok', 'urutan']);
            });
        }

        if (! Schema::hasTable('audit_rencana_pengesahans')) {
            Schema::create('audit_rencana_pengesahans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('audit_rencana_id')->constrained('audit_rencanas')->cascadeOnDelete();
                $table->unsignedSmallInteger('urutan')->default(0);
                $table->string('jabatan');
                $table->string('nama')->nullable();
                $table->date('tanggal_ttd')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_rencana_pengesahans');
        Schema::dropIfExists('master_penandatangan_rencana');
        Schema::dropIfExists('audit_rencana_sampels');
        Schema::dropIfExists('audit_rencana_jadwals');
        Schema::dropIfExists('audit_rencana_tims');
        Schema::dropIfExists('audit_rencanas');
    }
};
