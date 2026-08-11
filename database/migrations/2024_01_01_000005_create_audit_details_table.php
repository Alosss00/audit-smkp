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
        Schema::create('audit_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_sesi_id')->constrained('audit_sesis')->onDelete('cascade');
            $table->foreignId('kriteria_id')->constrained('kriterias')->onDelete('restrict');
            $table->decimal('nilai', 5, 2)->default(0);
            $table->boolean('is_na')->default(false);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['audit_sesi_id', 'kriteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_details');
    }
};
