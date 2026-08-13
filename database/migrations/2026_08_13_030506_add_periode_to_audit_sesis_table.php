<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_sesis', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('user_id');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
        });

        // Migrate existing data: copy tanggal_audit to both new columns
        if (Schema::hasColumn('audit_sesis', 'tanggal_audit')) {
            DB::statement('UPDATE audit_sesis SET tanggal_mulai = tanggal_audit, tanggal_selesai = tanggal_audit WHERE tanggal_audit IS NOT NULL');
        }

        Schema::table('audit_sesis', function (Blueprint $table) {
            $table->dropColumn('tanggal_audit');
            // Make non-nullable after data migration
            $table->date('tanggal_mulai')->nullable(false)->change();
            $table->date('tanggal_selesai')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('audit_sesis', function (Blueprint $table) {
            $table->date('tanggal_audit')->nullable()->after('user_id');
        });

        DB::statement('UPDATE audit_sesis SET tanggal_audit = tanggal_mulai');

        Schema::table('audit_sesis', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
        });
    }
};
