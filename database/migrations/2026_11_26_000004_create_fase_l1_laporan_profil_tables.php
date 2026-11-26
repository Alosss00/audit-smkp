<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('perusahaan_legalitas')) {
            Schema::create('perusahaan_legalitas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('perusahaan_id')->unique()->constrained('perusahaans')->cascadeOnDelete();
                $table->enum('jenis_izin', [
                    'iup_eksplorasi',
                    'iup_operasi_produksi',
                    'iupk',
                    'iup_pengolahan_pemurnian',
                    'ipr',
                    'iujp',
                    'pkp2b',
                    'kontrak_karya',
                    'lainnya',
                ])->nullable();
                $table->string('nomor_sk_izin')->nullable();
                $table->date('tanggal_sk')->nullable();
                $table->date('tanggal_mulai_berlaku')->nullable();
                $table->date('tanggal_berakhir')->nullable();
                $table->string('status_izin')->nullable();
                $table->string('kode_wiup')->nullable();
                $table->decimal('luas_ha', 12, 2)->nullable();
                $table->string('lokasi_desa')->nullable();
                $table->string('lokasi_kecamatan')->nullable();
                $table->string('lokasi_kabupaten')->nullable();
                $table->string('lokasi_provinsi')->nullable();
                $table->string('nomor_persetujuan_studi_kelayakan')->nullable();
                $table->date('tanggal_persetujuan_studi_kelayakan')->nullable();
                $table->string('nomor_persetujuan_dokumen_lingkungan')->nullable();
                $table->date('tanggal_persetujuan_dokumen_lingkungan')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('audit_laporans')) {
            Schema::create('audit_laporans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('audit_sesi_id')->unique()->constrained('audit_sesis')->cascadeOnDelete();
                $table->string('nomor_laporan')->nullable();
                $table->date('tanggal_laporan')->nullable();
                $table->string('tempat_penerbitan')->nullable();
                $table->enum('status', ['draft', 'ditinjau', 'disahkan', 'didistribusikan', 'dilaporkan'])->default('draft');
                $table->foreignId('diajukan_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('tanggal_diajukan')->nullable();
                $table->foreignId('ditinjau_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('tanggal_ditinjau')->nullable();
                $table->foreignId('disahkan_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('tanggal_pengesahan')->nullable();
                $table->date('tanggal_dilaporkan')->nullable();
                $table->date('batas_waktu_pelaporan')->nullable();
                $table->string('penanggung_jawab_nama')->nullable();
                $table->json('teks_override')->nullable();
                $table->unsignedSmallInteger('revisi_ke')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('audit_laporan_profils')) {
            Schema::create('audit_laporan_profils', function (Blueprint $table) {
                $table->id();
                $table->foreignId('audit_laporan_id')->unique()->constrained('audit_laporans')->cascadeOnDelete();
                $table->decimal('kapasitas_produksi', 14, 2)->nullable();
                $table->decimal('realisasi_produksi', 14, 2)->nullable();
                $table->string('satuan_produksi', 20)->nullable();
                $table->unsignedInteger('jumlah_pekerja')->nullable();
                $table->unsignedInteger('jumlah_pekerja_asing')->nullable();
                $table->text('narasi_kegiatan')->nullable();
                $table->text('narasi_penerapan_smkp')->nullable();
                $table->string('struktur_organisasi_path')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('audit_laporan_peralatans')) {
            Schema::create('audit_laporan_peralatans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('audit_laporan_id')->constrained('audit_laporans')->cascadeOnDelete();
                $table->string('kelompok', 100);
                $table->string('jenis', 150);
                $table->string('tipe', 100)->nullable();
                $table->string('kapasitas', 100)->nullable();
                $table->unsignedInteger('jumlah');
                $table->unsignedSmallInteger('urutan')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('audit_laporan_narasi_elemens')) {
            Schema::create('audit_laporan_narasi_elemens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('audit_laporan_id')->constrained('audit_laporans')->cascadeOnDelete();
                $table->foreignId('elemen_id')->constrained('elemens')->cascadeOnDelete();
                $table->text('narasi');
                $table->timestamps();

                $table->unique(['audit_laporan_id', 'elemen_id'], 'laporan_narasi_elemen_unique');
            });
        }

        if (! Schema::hasTable('audit_rencana_areas')) {
            Schema::create('audit_rencana_areas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('audit_rencana_id')->constrained('audit_rencanas')->cascadeOnDelete();
                $table->string('nama_area');
                $table->unsignedSmallInteger('urutan')->default(0);
                $table->timestamps();
            });
        }

        // Perluas enum peran pada audit_rencana_tims untuk menambahkan 'pemandu'
        if (Schema::hasTable('audit_rencana_tims') && DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `audit_rencana_tims` MODIFY COLUMN `peran` ENUM('lead_auditor','auditor','tenaga_ahli','pengamat','pemandu') NOT NULL DEFAULT 'auditor'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('audit_rencana_tims') && DB::getDriverName() === 'mysql') {
            DB::table('audit_rencana_tims')->where('peran', 'pemandu')->update(['peran' => 'pengamat']);
            DB::statement("ALTER TABLE `audit_rencana_tims` MODIFY COLUMN `peran` ENUM('lead_auditor','auditor','tenaga_ahli','pengamat') NOT NULL DEFAULT 'auditor'");
        }

        Schema::dropIfExists('audit_rencana_areas');
        Schema::dropIfExists('audit_laporan_narasi_elemens');
        Schema::dropIfExists('audit_laporan_peralatans');
        Schema::dropIfExists('audit_laporan_profils');
        Schema::dropIfExists('audit_laporans');
        Schema::dropIfExists('perusahaan_legalitas');
    }
};
