<?php

namespace Database\Seeders;

use App\Models\AuditDetail;
use App\Models\AuditSesi;
use App\Models\Pica;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleAuditSeeder extends Seeder
{
    /**
     * Run the database seeds - cleared for clean setup.
     */
    public function run(): void
    {
        // Sample data disabled: Clean existing sample audit data if any
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Pica::truncate();
        AuditDetail::truncate();
        AuditSesi::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
