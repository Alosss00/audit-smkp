<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Refaktorisasi arsitektur relasi antar kriteria SMKP.
     *
     * KONTEKS DESAIN:
     * Desain sebelumnya (kolom dependency_id pada tabel kriterias) mengasumsikan
     * keterkaitan bersifat single-parent (1:1). Analisis data SMKP membuktikan:
     *   - 699 relasi tersebar (rata-rata 7,1 relasi/kriteria).
     *   - Relasi bersifat many-to-many, asimetris (terarah A -> B).
     *   - Relasi murni advisory (panduan jejak penelusuran), NON-blocking.
     *
     * CATATAN: KriteriaGatingRule TETAP UTUH (beda tujuan: hard/soft business rule).
     */
    public function up(): void
    {
        // STEP 1: Drop kolom dependency single-parent yang sudah usang
        if (Schema::hasColumn('kriterias', 'dependency_id')) {
            Schema::table('kriterias', function (Blueprint $table) {
                $table->dropForeign(['dependency_id']);
                $table->dropColumn(['dependency_id', 'dependency_note']);
            });
        }

        // STEP 2: Buat tabel pivot many-to-many relasi antar kriteria
        Schema::create('kriteria_relasi', function (Blueprint $table) {
            $table->id();

            $table->string('kriteria_asal_kode', 30)
                ->comment('Kode kriteria yang sedang diaudit / titik berangkat penelusuran');

            $table->string('kriteria_tujuan_kode', 30)
                ->comment('Kode kriteria terkait yang direkomendasikan untuk diperiksa');

            // kunci     = Banner peringatan aktif saat nilai diisi (20 relasi strategis)
            // referensi = Popover pasif sebagai referensi visual (699 relasi)
            $table->enum('jenis_relasi', ['kunci', 'referensi'])
                ->default('referensi')
                ->index()
                ->comment('kunci = banner aktif; referensi = popover pasif');

            $table->text('deskripsi_keterkaitan')
                ->nullable()
                ->comment('Narasi sebab-akibat atau justifikasi metodologis keterkaitan ini');

            $table->timestamps();

            // Indeks performa untuk query GET /api/kriteria/{kode}/relasi
            $table->index(['kriteria_asal_kode', 'jenis_relasi'], 'idx_relasi_asal_jenis');
            $table->index('kriteria_tujuan_kode', 'idx_relasi_tujuan');

            // Unique constraint: satu pasang (asal, tujuan, jenis) cukup 1 baris
            $table->unique(
                ['kriteria_asal_kode', 'kriteria_tujuan_kode', 'jenis_relasi'],
                'uq_kriteria_relasi_pair'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kriteria_relasi');

        Schema::table('kriterias', function (Blueprint $table) {
            $table->foreignId('dependency_id')
                ->nullable()
                ->after('nilai_maksimal')
                ->constrained('kriterias')
                ->onDelete('set null');

            $table->text('dependency_note')
                ->nullable()
                ->after('dependency_id');
        });
    }
};
