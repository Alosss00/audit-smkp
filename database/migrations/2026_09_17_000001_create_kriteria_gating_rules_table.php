<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel ini menyimpan aturan ketergantungan (Cross-Check Gating Rules) antar kriteria/sub-elemen
     * berdasarkan kajian metodologis SMKP Minerba.
     *
     * Catatan Asumsi Skema:
     * - Kajian metodologis mendefinisikan aturan di level Sub-Elemen (misal I.2, II.4), namun
     *   penilaian aktual pada sistem disimpan pada level Kriteria (tabel kriterias / audit_details).
     * - Relasi dibuat ke `kriterias.id` on delete restrict agar konsistensi data riwayat audit terjaga.
     * - Nilai hulu dan hilir dapat diformulasikan sebagai ambang pemicu batas atas (skor_maks_hilir)
     *   dengan mode `hard_block` (validasi wajib) atau `soft_flag` (peringatan inkonsistensi sistem).
     */
    public function up(): void
    {
        Schema::create('kriteria_gating_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kriteria_hulu_id')
                ->nullable()
                ->comment('Kriteria prasyarat/hulu pemicu gating (FK kriterias.id)')
                ->constrained('kriterias')
                ->onDelete('restrict');

            $table->foreignId('kriteria_hilir_id')
                ->nullable()
                ->comment('Kriteria terdampak/hilir yang dibatasi nilainya (FK kriterias.id)')
                ->constrained('kriterias')
                ->onDelete('restrict');

            $table->decimal('ambang_hulu', 5, 2)
                ->nullable()
                ->comment('Skor hulu <= ambang_hulu akan men-trigger batas gating hilir');

            $table->decimal('skor_maks_hilir', 5, 2)
                ->nullable()
                ->comment('Batas maksimal skor yang diizinkan untuk kriteria hilir jika gating aktif');

            $table->enum('mode', ['hard_block', 'soft_flag'])
                ->default('soft_flag')
                ->comment('hard_block = cegah simpan jika melanggar, soft_flag = catat peringatan/warning sistem');

            $table->text('deskripsi_simpul')
                ->comment('Deskripsi nomor simpul, rujukan sub-elemen Kepdirjen 185, dan justifikasi metodologis');

            $table->boolean('is_active')
                ->default(true)
                ->comment('Flag aktivasi aturan gating (bisa di-toggle aktif/nonaktif)');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kriteria_gating_rules');
    }
};
