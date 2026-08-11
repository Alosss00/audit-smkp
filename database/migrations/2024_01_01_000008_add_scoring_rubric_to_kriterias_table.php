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
        Schema::table('kriterias', function (Blueprint $table) {
            $table->text('persyaratan_dokumen')->nullable()->after('nilai_maksimal');
            $table->text('pedoman_nilai_0')->nullable()->after('persyaratan_dokumen');
            $table->text('pedoman_nilai_1')->nullable()->after('pedoman_nilai_0');
            $table->text('pedoman_nilai_2')->nullable()->after('pedoman_nilai_1');
            $table->text('pedoman_nilai_3')->nullable()->after('pedoman_nilai_2');
            $table->text('pedoman_nilai_4')->nullable()->after('pedoman_nilai_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kriterias', function (Blueprint $table) {
            $table->dropColumn([
                'persyaratan_dokumen',
                'pedoman_nilai_0',
                'pedoman_nilai_1',
                'pedoman_nilai_2',
                'pedoman_nilai_3',
                'pedoman_nilai_4',
            ]);
        });
    }
};
