<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('audit_tahap_awals')) {
            return;
        }

        Schema::table('audit_tahap_awals', function (Blueprint $table) {
            $table->string('no_dokumen_header')->nullable()->default(null)->change();
            $table->string('no_dokumen_footer')->nullable()->default(null)->change();
            $table->string('no_revisi')->nullable()->default(null)->change();
            $table->integer('jumlah_pekerja')->nullable()->default(null)->change();
            $table->string('kelas_risiko')->nullable()->default(null)->change();
            $table->decimal('mandays_awal', 6, 1)->nullable()->default(null)->change();
            $table->decimal('faktor_penyesuaian_hari', 6, 1)->nullable()->default(null)->change();
            $table->decimal('total_mandays', 6, 1)->nullable()->default(null)->change();
            $table->decimal('alokasi_tahap_1', 6, 1)->nullable()->default(null)->change();
            $table->string('alokasi_tahap_2')->nullable()->default(null)->change();
            $table->string('kesimpulan_kelayakan')->nullable()->default(null)->change();
            $table->string('kesimpulan_kecukupan')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        // Keep columns nullable on rollback to avoid failing on existing null records.
    }
};
