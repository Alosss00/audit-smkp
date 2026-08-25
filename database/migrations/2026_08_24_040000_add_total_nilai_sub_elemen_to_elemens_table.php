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
        Schema::table('elemens', function (Blueprint $table) {
            if (!Schema::hasColumn('elemens', 'total_nilai_sub_elemen')) {
                $table->decimal('total_nilai_sub_elemen', 8, 2)->default(0.00)->nullable()->after('bobot');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elemens', function (Blueprint $table) {
            if (Schema::hasColumn('elemens', 'total_nilai_sub_elemen')) {
                $table->dropColumn('total_nilai_sub_elemen');
            }
        });
    }
};
