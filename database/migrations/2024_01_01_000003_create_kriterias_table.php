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
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_elemen_id')->constrained('sub_elemens')->onDelete('cascade');
            $table->string('kode_kriteria');
            $table->text('deskripsi');
            $table->decimal('nilai_maksimal', 5, 2);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['sub_elemen_id', 'kode_kriteria']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kriterias');
    }
};
