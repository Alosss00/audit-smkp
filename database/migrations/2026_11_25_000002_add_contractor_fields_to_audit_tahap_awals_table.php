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
            if (! Schema::hasColumn('audit_tahap_awals', 'perusahaan_induk')) {
                $table->string('perusahaan_induk')->nullable()->after('nama_perusahaan_auditi');
            }
            if (! Schema::hasColumn('audit_tahap_awals', 'tanggal_surat_tugas_tahap_2')) {
                $table->date('tanggal_surat_tugas_tahap_2')->nullable()->after('tanggal_surat_tugas');
            }
            if (! Schema::hasColumn('audit_tahap_awals', 'nama_pjo')) {
                $table->string('nama_pjo')->nullable()->after('ketua_tim_audit');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('audit_tahap_awals')) {
            return;
        }

        Schema::table('audit_tahap_awals', function (Blueprint $table) {
            $cols = array_values(array_filter(
                ['perusahaan_induk', 'tanggal_surat_tugas_tahap_2', 'nama_pjo'],
                fn (string $col) => Schema::hasColumn('audit_tahap_awals', $col)
            ));
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
