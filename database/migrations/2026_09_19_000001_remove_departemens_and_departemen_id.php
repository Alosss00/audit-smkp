<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Delete all audit sessions linked to departemens or without perusahaan
        $departemenSesiIds = DB::table('audit_sesis')
            ->whereNotNull('departemen_id')
            ->pluck('id');

        if ($departemenSesiIds->isNotEmpty()) {
            $detailIds = DB::table('audit_details')
                ->whereIn('audit_sesi_id', $departemenSesiIds)
                ->pluck('id');

            if ($detailIds->isNotEmpty()) {
                DB::table('picas')->whereIn('audit_detail_id', $detailIds)->delete();
                DB::table('audit_details')->whereIn('id', $detailIds)->delete();
            }

            DB::table('audit_sesis')->whereIn('id', $departemenSesiIds)->delete();
        }

        // 2. Drop foreign keys and columns
        if (Schema::hasColumn('audit_sesis', 'departemen_id')) {
            Schema::table('audit_sesis', function (Blueprint $table) {
                // Drop foreign key if exists
                try {
                    $table->dropForeign(['departemen_id']);
                } catch (\Throwable $e) {
                    // Ignored if fk doesn't exist
                }
                $table->dropColumn('departemen_id');
            });
        }

        if (Schema::hasColumn('users', 'departemen_id')) {
            Schema::table('users', function (Blueprint $table) {
                try {
                    $table->dropForeign(['departemen_id']);
                } catch (\Throwable $e) {
                    // Ignored if fk doesn't exist
                }
                $table->dropColumn('departemen_id');
            });
        }

        // 3. Drop departemens table
        Schema::dropIfExists('departemens');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('departemens')) {
            Schema::create('departemens', function (Blueprint $table) {
                $table->id();
                $table->string('nama_departemen');
                $table->string('kode_departemen', 50)->nullable();
                $table->text('deskripsi')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('audit_sesis', 'departemen_id')) {
            Schema::table('audit_sesis', function (Blueprint $table) {
                $table->foreignId('departemen_id')->nullable()->after('perusahaan_id')->constrained('departemens')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('users', 'departemen_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('departemen_id')->nullable()->after('role')->constrained('departemens')->nullOnDelete();
            });
        }
    }
};
