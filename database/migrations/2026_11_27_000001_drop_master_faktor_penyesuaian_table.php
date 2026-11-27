<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('master_faktor_penyesuaian');
    }

    public function down(): void
    {
        Schema::create('master_faktor_penyesuaian', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 60)->unique();
            $table->string('nama', 255);
            $table->enum('tipe', ['tambah', 'kurang'])->default('tambah');
            $table->decimal('nilai_hari', 4, 1)->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
