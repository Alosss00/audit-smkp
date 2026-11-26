<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('picas')) {
            Schema::table('picas', function (Blueprint $table) {
                if (! Schema::hasColumn('picas', 'audit_sesi_id')) {
                    $table->foreignId('audit_sesi_id')->nullable()->after('id')->constrained('audit_sesis')->cascadeOnDelete();
                }
                if (! Schema::hasColumn('picas', 'nomor_urut')) {
                    $table->unsignedInteger('nomor_urut')->nullable()->after('audit_sesi_id');
                }
                if (! Schema::hasColumn('picas', 'plor_problem')) {
                    $table->text('plor_problem')->nullable()->after('deskripsi_ketidaksesuaian');
                }
                if (! Schema::hasColumn('picas', 'plor_location')) {
                    $table->string('plor_location')->nullable()->after('plor_problem');
                }
                if (! Schema::hasColumn('picas', 'plor_objective_evidence')) {
                    $table->text('plor_objective_evidence')->nullable()->after('plor_location');
                }
                if (! Schema::hasColumn('picas', 'plor_reference')) {
                    $table->string('plor_reference')->nullable()->after('plor_objective_evidence');
                }
                if (! Schema::hasColumn('picas', 'diverifikasi_oleh')) {
                    $table->foreignId('diverifikasi_oleh')->nullable()->after('catatan_verifikasi_auditor')->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('picas', 'tanggal_verifikasi')) {
                    $table->date('tanggal_verifikasi')->nullable()->after('diverifikasi_oleh');
                }
                if (! Schema::hasColumn('picas', 'persetujuan_lead_auditor_oleh')) {
                    $table->foreignId('persetujuan_lead_auditor_oleh')->nullable()->after('tanggal_verifikasi')->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('picas', 'tanggal_persetujuan_lead')) {
                    $table->date('tanggal_persetujuan_lead')->nullable()->after('persetujuan_lead_auditor_oleh');
                }
                if (! Schema::hasColumn('picas', 'persetujuan_auditi_nama')) {
                    $table->string('persetujuan_auditi_nama')->nullable()->after('tanggal_persetujuan_lead');
                }
                if (! Schema::hasColumn('picas', 'tanggal_persetujuan_auditi')) {
                    $table->date('tanggal_persetujuan_auditi')->nullable()->after('persetujuan_auditi_nama');
                }
                if (! Schema::hasColumn('picas', 'deleted_at')) {
                    $table->softDeletes();
                }
            });

            // Backfill audit_sesi_id dari audit_details.audit_sesi_id
            $rows = DB::table('picas')
                ->leftJoin('audit_details', 'picas.audit_detail_id', '=', 'audit_details.id')
                ->select('picas.id', 'picas.audit_sesi_id', 'picas.nomor_urut', 'audit_details.audit_sesi_id as detail_sesi_id')
                ->orderBy('picas.id')
                ->get();

            $counters = [];
            foreach ($rows as $row) {
                $sesiId = $row->audit_sesi_id ?? $row->detail_sesi_id;
                if (! $sesiId) {
                    continue;
                }
                if (! isset($counters[$sesiId])) {
                    $counters[$sesiId] = 0;
                }
                $counters[$sesiId]++;
                $nomor = $row->nomor_urut ?: $counters[$sesiId];
                if ($nomor > $counters[$sesiId]) {
                    $counters[$sesiId] = $nomor;
                }

                DB::table('picas')
                    ->where('id', $row->id)
                    ->update([
                        'audit_sesi_id' => $sesiId,
                        'nomor_urut'    => $nomor,
                    ]);
            }

            // Tambahkan unique index (audit_sesi_id, nomor_urut)
            Schema::table('picas', function (Blueprint $table) {
                $table->unique(['audit_sesi_id', 'nomor_urut'], 'picas_sesi_nomor_urut_unique');
            });
        }

        if (! Schema::hasTable('audit_ofis')) {
            Schema::create('audit_ofis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('audit_sesi_id')->constrained('audit_sesis')->cascadeOnDelete();
                $table->unsignedInteger('nomor_urut');
                $table->foreignId('sub_elemen_id')->nullable()->constrained('sub_elemens')->nullOnDelete();
                $table->foreignId('kriteria_id')->nullable()->constrained('kriterias')->nullOnDelete();
                $table->text('deskripsi');
                $table->text('rekomendasi');
                $table->string('referensi')->nullable();
                $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['audit_sesi_id', 'nomor_urut'], 'audit_ofis_sesi_nomor_urut_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_ofis');

        if (Schema::hasTable('picas')) {
            Schema::table('picas', function (Blueprint $table) {
                try {
                    $table->dropUnique('picas_sesi_nomor_urut_unique');
                } catch (\Throwable $e) {
                    // ignore if not exists
                }
                foreach (['audit_sesi_id', 'diverifikasi_oleh', 'persetujuan_lead_auditor_oleh'] as $fkCol) {
                    if (Schema::hasColumn('picas', $fkCol)) {
                        try {
                            $table->dropForeign([$fkCol]);
                        } catch (\Throwable $e) {
                            // ignore
                        }
                    }
                }
                $cols = array_values(array_filter([
                    'audit_sesi_id',
                    'nomor_urut',
                    'plor_problem',
                    'plor_location',
                    'plor_objective_evidence',
                    'plor_reference',
                    'diverifikasi_oleh',
                    'tanggal_verifikasi',
                    'persetujuan_lead_auditor_oleh',
                    'tanggal_persetujuan_lead',
                    'persetujuan_auditi_nama',
                    'tanggal_persetujuan_auditi',
                    'deleted_at',
                ], fn (string $c) => Schema::hasColumn('picas', $c)));

                if (! empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
