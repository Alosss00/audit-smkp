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
        Schema::create('sub_elemens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elemen_id')->constrained('elemens')->onDelete('cascade');
            $table->string('kode_sub');
            $table->string('nama_sub');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['elemen_id', 'kode_sub']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_elemens');
    }
};
