<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('perusahaans') && ! Schema::hasColumn('perusahaans', 'jenis_usaha')) {
            Schema::table('perusahaans', function (Blueprint $table) {
                $table->enum('jenis_usaha', [
                    'tambang',
                    'pengolahan_pemurnian',
                    'jasa_pertambangan_inti',
                    'jasa_konsultasi_perencanaan',
                    'jasa_non_inti',
                ])->nullable()->after('kategori');
            });
        }

        if (! Schema::hasTable('master_hari_kerja_audit')) {
            Schema::create('master_hari_kerja_audit', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('jumlah_min');
                $table->unsignedInteger('jumlah_max')->nullable();
                $table->decimal('hari_tinggi', 5, 1);
                $table->decimal('hari_menengah', 5, 1);
                $table->decimal('hari_rendah', 5, 1);
                $table->boolean('is_verified')->default(false);
                $table->string('sumber')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('master_faktor_penyesuaian')) {
            Schema::create('master_faktor_penyesuaian', function (Blueprint $table) {
                $table->id();
                $table->string('kode')->unique();
                $table->string('nama');
                $table->enum('tipe', ['tambah', 'kurang']);
                $table->decimal('nilai_hari', 4, 1)->nullable();
                $table->unsignedSmallInteger('urutan')->default(0);
                $table->boolean('is_active')->default(true);
                $table->text('keterangan')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('audit_tahap_awals') && ! Schema::hasColumn('audit_tahap_awals', 'sumber_perhitungan')) {
            Schema::table('audit_tahap_awals', function (Blueprint $table) {
                $table->string('sumber_perhitungan', 20)->nullable()->after('alokasi_tahap_2');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('audit_tahap_awals') && Schema::hasColumn('audit_tahap_awals', 'sumber_perhitungan')) {
            Schema::table('audit_tahap_awals', function (Blueprint $table) {
                $table->dropColumn('sumber_perhitungan');
            });
        }

        Schema::dropIfExists('master_faktor_penyesuaian');
        Schema::dropIfExists('master_hari_kerja_audit');

        if (Schema::hasTable('perusahaans') && Schema::hasColumn('perusahaans', 'jenis_usaha')) {
            Schema::table('perusahaans', function (Blueprint $table) {
                $table->dropColumn('jenis_usaha');
            });
        }
    }
};
