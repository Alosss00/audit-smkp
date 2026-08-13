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
        // Round existing decimal values before changing type
        \Illuminate\Support\Facades\DB::statement('UPDATE audit_details SET nilai = ROUND(nilai)');
        \Illuminate\Support\Facades\DB::statement('UPDATE kriterias SET nilai_maksimal = ROUND(nilai_maksimal)');

        Schema::table('audit_details', function (Blueprint $table) {
            $table->unsignedTinyInteger('nilai')->default(0)->change();
        });

        Schema::table('kriterias', function (Blueprint $table) {
            $table->unsignedTinyInteger('nilai_maksimal')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integer', function (Blueprint $table) {
            //
        });
    }
};
