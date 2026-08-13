<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('picas', function (Blueprint $table) {
            $table->string('bukti_perbaikan')->nullable()->after('tindakan_pencegahan');
            $table->dropColumn('pic_perbaikan');
        });
    }

    public function down(): void
    {
        Schema::table('picas', function (Blueprint $table) {
            $table->string('pic_perbaikan')->nullable()->after('tindakan_pencegahan');
            $table->dropColumn('bukti_perbaikan');
        });
    }
};
