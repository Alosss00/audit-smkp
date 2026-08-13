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
            $table->index('area_audit', 'idx_audit_sesis_area_audit');
            $table->index('status', 'idx_audit_sesis_status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('area', 'idx_users_area');
            $table->index('role', 'idx_users_role');
        });

        Schema::table('picas', function (Blueprint $table) {
            $table->index('status', 'idx_picas_status');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index('modul', 'idx_audit_logs_modul');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_sesis', function (Blueprint $table) {
            $table->dropIndex('idx_audit_sesis_area_audit');
            $table->dropIndex('idx_audit_sesis_status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_area');
            $table->dropIndex('idx_users_role');
        });

        Schema::table('picas', function (Blueprint $table) {
            $table->dropIndex('idx_picas_status');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('idx_audit_logs_modul');
        });
    }
};
