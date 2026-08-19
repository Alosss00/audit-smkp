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
        Schema::table('audit_sesis', function (Blueprint $table) {
            $table->foreignId('departemen_id')->nullable()->after('perusahaan_id')->constrained('departemens')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('departemen_id')->nullable()->after('role')->constrained('departemens')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_sesis', function (Blueprint $table) {
            $table->dropForeign(['departemen_id']);
            $table->dropColumn('departemen_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['departemen_id']);
            $table->dropColumn('departemen_id');
        });
    }
};
