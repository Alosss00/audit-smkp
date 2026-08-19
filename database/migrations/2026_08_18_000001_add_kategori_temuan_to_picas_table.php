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
            $table->enum('kategori_temuan', ['kritikal', 'mayor', 'minor'])
                ->default('minor')
                ->after('deskripsi_temuan');
            $table->boolean('kategori_ditetapkan_manual')
                ->default(false)
                ->after('kategori_temuan');
            $table->text('justifikasi_kategori')
                ->nullable()
                ->after('kategori_ditetapkan_manual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('picas', function (Blueprint $table) {
            $table->dropColumn([
                'kategori_temuan',
                'kategori_ditetapkan_manual',
                'justifikasi_kategori',
            ]);
        });
    }
};
