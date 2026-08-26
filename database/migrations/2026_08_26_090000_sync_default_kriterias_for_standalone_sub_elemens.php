<?php

use App\Models\Kriteria;
use App\Models\SubElemen;
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
        if (!Schema::hasColumn('kriterias', 'pedoman_nilai_json')) {
            Schema::table('kriterias', function (Blueprint $table) {
                $table->json('pedoman_nilai_json')->nullable()->after('pedoman_nilai_4');
            });
        }

        // Sync direct assessment Kriterias for any SubElemen that currently has no child Kriterias
        $subElemens = SubElemen::withCount('kriterias')->get();
        foreach ($subElemens as $sub) {
            if ($sub->kriterias_count === 0) {
                $maxScore = (float) $sub->nilai_maksimal > 0 ? (float) $sub->nilai_maksimal : 4.0;
                Kriteria::create([
                    'sub_elemen_id'  => $sub->id,
                    'kode_kriteria'  => $sub->kode_sub,
                    'deskripsi'      => $sub->nama_sub,
                    'nilai_maksimal' => $maxScore,
                    'is_na'          => $sub->is_na,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('kriterias', 'pedoman_nilai_json')) {
            Schema::table('kriterias', function (Blueprint $table) {
                $table->dropColumn('pedoman_nilai_json');
            });
        }
    }
};
