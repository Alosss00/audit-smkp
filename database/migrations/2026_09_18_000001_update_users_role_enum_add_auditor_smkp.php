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
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin', 'auditor_smkp', 'auditor') NOT NULL DEFAULT 'auditor'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 50)->default('auditor')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin', 'auditor') NOT NULL DEFAULT 'auditor'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 50)->default('auditor')->change();
            });
        }
    }
};
