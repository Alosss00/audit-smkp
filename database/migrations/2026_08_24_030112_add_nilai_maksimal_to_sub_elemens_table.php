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
        Schema::table('sub_elemens', function (Blueprint $table) {
            if (!Schema::hasColumn('sub_elemens', 'nilai_maksimal')) {
                $table->decimal('nilai_maksimal', 5, 2)->default(4.00)->nullable()->after('nama_sub');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_elemens', function (Blueprint $table) {
            if (Schema::hasColumn('sub_elemens', 'nilai_maksimal')) {
                $table->dropColumn('nilai_maksimal');
            }
        });
    }
};
