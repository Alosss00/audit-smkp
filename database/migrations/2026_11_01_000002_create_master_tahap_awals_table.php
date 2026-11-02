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
        if (!Schema::hasTable('master_tahap_awals')) {
            Schema::create('master_tahap_awals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('perusahaan_id')->unique()->constrained('perusahaans')->cascadeOnDelete();

                // Metadata Dokumen
                $table->string('no_dokumen_header')->default('TT-MGT-FRS-026L');
                $table->string('no_dokumen_footer')->default('TT-MGT-FRS-026K');
                $table->string('no_revisi')->default('01');
                $table->date('tanggal_terbit')->nullable();
                $table->date('tanggal_tinjau_ulang')->nullable();

                // Data Perusahaan Auditi
                $table->string('nama_perusahaan_auditi')->nullable();
                $table->text('jenis_perizinan')->nullable();
                $table->string('jenis_komoditas')->nullable();
                $table->text('alamat_kantor_site')->nullable();
                $table->text('alamat_lokasi_tambang')->nullable();
                $table->text('alamat_kantor_pusat')->nullable();
                $table->string('periode_audit_label')->nullable();

                // Bab I.A - Data Kinerja KP & Faktor Penyesuaian
                $table->json('kinerja_kp')->nullable();
                $table->json('faktor_penyesuaian')->nullable();

                // Bab I.B - Perhitungan Hari Kerja Audit & Jumlah Auditor
                $table->integer('jumlah_pekerja')->default(3086);
                $table->string('kelas_risiko')->default('Tinggi');
                $table->decimal('mandays_awal', 6, 1)->default(25.0);
                $table->decimal('faktor_penyesuaian_hari', 6, 1)->default(2.5);
                $table->decimal('total_mandays', 6, 1)->default(27.5);
                $table->decimal('alokasi_tahap_1', 6, 1)->default(3.0);
                $table->string('alokasi_tahap_2')->default('24,5 Hari ( 4.9 Hari, 5 Auditor)');

                // Bab II - Hasil Permulaan Audit & Peninjauan Dokumen
                $table->string('no_surat_tugas')->nullable();
                $table->date('tanggal_surat_tugas')->nullable();
                $table->json('tim_audit_tahap_1')->nullable();
                $table->json('kontak_awal')->nullable();
                $table->json('kelayakan_audit')->nullable();
                $table->string('kesimpulan_kelayakan')->default('layak');
                $table->json('kecukupan_dokumen')->nullable();
                $table->string('kesimpulan_kecukupan')->default('dapat_dilanjutkan');

                // Bab III - Persiapan Audit Lapangan
                $table->json('tim_audit_tahap_2')->nullable();
                $table->json('top_risks_present')->nullable();
                $table->json('top_risks_future')->nullable();
                $table->json('trend_data')->nullable();
                $table->json('unjuk_kerja_peralatan')->nullable();
                $table->json('capaian_kli')->nullable();
                $table->json('data_audit_sebelumnya')->nullable();
                $table->json('harapan_pemangku')->nullable();

                // Penutup & Pengesahan
                $table->date('tanggal_berita_acara')->nullable();
                $table->string('ketua_tim_audit')->nullable();
                $table->string('nama_ktt')->nullable();
                $table->boolean('is_customized')->default(false);

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_tahap_awals');
    }
};

