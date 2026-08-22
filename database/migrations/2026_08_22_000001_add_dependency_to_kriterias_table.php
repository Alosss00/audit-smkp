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
        Schema::table('kriterias', function (Blueprint $table) {
            $table->foreignId('dependency_id')->nullable()->after('nilai_maksimal')
                ->constrained('kriterias')->onDelete('set null');
            $table->text('dependency_note')->nullable()->after('dependency_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kriterias', function (Blueprint $table) {
            $table->dropForeign(['dependency_id']);
            $table->dropColumn(['dependency_id', 'dependency_note']);
        });
    }
};
