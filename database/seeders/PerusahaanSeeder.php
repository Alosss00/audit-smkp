<?php

namespace Database\Seeders;

use App\Models\Perusahaan;
use Illuminate\Database\Seeder;

class PerusahaanSeeder extends Seeder
{
    /**
     * Run the database seeds for primary companies.
     */
    public function run(): void
    {
        $companies = [
            ['nama' => 'PT Meares Soputan Mining', 'kategori' => 'Pemegang IUP'],
            ['nama' => 'PT Tambang Tondano Nusa Jaya', 'kategori' => 'Pemegang IUP'],
        ];

        foreach ($companies as $comp) {
            Perusahaan::firstOrCreate(
                ['nama_perusahaan' => $comp['nama']],
                ['kategori' => $comp['kategori'], 'is_active' => true]
            );
        }
    }
}

