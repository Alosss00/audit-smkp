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
            if (!Schema::hasColumn('sub_elemens', 'is_na')) {
                $table->boolean('is_na')->default(false)->after('nilai_maksimal');
            }
        });

        Schema::table('kriterias', function (Blueprint $table) {
            if (!Schema::hasColumn('kriterias', 'is_na')) {
                $table->boolean('is_na')->default(false)->after('nilai_maksimal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_elemens', function (Blueprint $table) {
            if (Schema::hasColumn('sub_elemens', 'is_na')) {
                $table->dropColumn('is_na');
            }
        });

        Schema::table('kriterias', function (Blueprint $table) {
            if (Schema::hasColumn('kriterias', 'is_na')) {
                $table->dropColumn('is_na');
            }
        });
    }
};
