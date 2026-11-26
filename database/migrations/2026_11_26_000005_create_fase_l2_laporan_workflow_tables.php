<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_laporan_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_laporan_id')->constrained('audit_laporans')->cascadeOnDelete();
            $table->unsignedInteger('revisi_ke')->default(0);
            $table->longText('payload_json');
            $table->char('hash_sha256', 64);
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['audit_laporan_id', 'revisi_ke']);
        });

        Schema::create('audit_laporan_distribusis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_laporan_id')->constrained('audit_laporans')->cascadeOnDelete();
            $table->string('penerima_nama', 150);
            $table->string('penerima_jabatan', 150)->nullable();
            $table->string('penerima_instansi', 200)->nullable();
            $table->enum('jenis_penerima', ['internal_perusahaan', 'ktt_pjo', 'ka_it', 'auditi', 'lainnya'])
                ->default('internal_perusahaan');
            $table->date('tanggal_distribusi');
            $table->enum('metode', ['hardcopy', 'email', 'sistem_esdm', 'lainnya'])->default('hardcopy');
            $table->string('nomor_tanda_terima', 100)->nullable();
            $table->string('bukti_path', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['audit_laporan_id', 'jenis_penerima']);
        });

        Schema::create('master_dasar_hukum', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 60)->unique();
            $table->string('nomor_peraturan', 150);
            $table->string('judul', 300);
            $table->text('ringkasan')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('master_template_laporan', function (Blueprint $table) {
            $table->id();
            $table->string('bagian', 60)->unique();
            $table->string('judul', 150);
            $table->longText('isi_template');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('master_aturan_pelaporan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 60)->unique();
            $table->string('nama_aturan', 150);
            $table->unsignedInteger('hari_setelah_akhir_periode')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('audit_laporans', function (Blueprint $table) {
            $table->text('catatan_revisi')->nullable()->after('revisi_ke');
        });

        Schema::table('audit_rencana_jadwals', function (Blueprint $table) {
            $table->enum('status_pelaksanaan', ['direncanakan', 'terlaksana', 'ditunda', 'dibatalkan'])
                ->default('direncanakan')
                ->after('lokasi');
        });
    }

    public function down(): void
    {
        Schema::table('audit_rencana_jadwals', function (Blueprint $table) {
            $table->dropColumn('status_pelaksanaan');
        });

        Schema::table('audit_laporans', function (Blueprint $table) {
            $table->dropColumn('catatan_revisi');
        });

        Schema::dropIfExists('master_aturan_pelaporan');
        Schema::dropIfExists('master_template_laporan');
        Schema::dropIfExists('master_dasar_hukum');
        Schema::dropIfExists('audit_laporan_distribusis');
        Schema::dropIfExists('audit_laporan_snapshots');
    }
};
