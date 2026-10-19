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
        Schema::table('picas', function (Blueprint $table) {
            if (!Schema::hasColumn('picas', 'pic_perbaikan')) {
                $table->string('pic_perbaikan')->nullable()->after('tindakan_pencegahan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('picas', function (Blueprint $table) {
            if (Schema::hasColumn('picas', 'pic_perbaikan')) {
                $table->dropColumn('pic_perbaikan');
            }
        });
    }
};
