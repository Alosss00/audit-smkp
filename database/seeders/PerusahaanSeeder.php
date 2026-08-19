<?php

namespace Database\Seeders;

use App\Models\Perusahaan;
use Illuminate\Database\Seeder;

class PerusahaanSeeder extends Seeder
{
    /**
     * Run the database seeds for 42 companies.
     */
    public function run(): void
    {
        $companies = [
            ['nama' => 'CV. Cahaya Dwi Perkasa', 'kategori' => 'Subkontraktor'],
            ['nama' => 'CV. Charisma', 'kategori' => 'Subkontraktor'],
            ['nama' => 'CV. Daya Kreasitama', 'kategori' => 'Subkontraktor'],
            ['nama' => 'CV. Puncak Kencana', 'kategori' => 'Subkontraktor'],
            ['nama' => 'On the Job Training', 'kategori' => 'Lainnya'],
            ['nama' => 'Police', 'kategori' => 'Lainnya'],
            ['nama' => 'PT Batu Biru Nusantara', 'kategori' => 'Kontraktor'],
            ['nama' => 'PT. AKR', 'kategori' => 'Kontraktor'],
            ['nama' => 'PT. Anggun Permai Tekindo', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Arlie Labora Utama', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Bromindo Mekar Mitra', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. DNX Indonesia', 'kategori' => 'Kontraktor'],
            ['nama' => 'PT. Eka Dharma Jaya Sakti', 'kategori' => 'Kontraktor'],
            ['nama' => 'PT. Energy Logistics', 'kategori' => 'Kontraktor'],
            ['nama' => 'PT. G4S', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Geopersada Mulia Abadi', 'kategori' => 'Kontraktor'],
            ['nama' => 'PT. Hanwha Mining Services Indonesia', 'kategori' => 'Kontraktor'],
            ['nama' => 'PT. Hexindo', 'kategori' => 'Kontraktor'],
            ['nama' => 'PT. Indos Cakra Mandiri', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Intertek', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Kilat Jaya', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Liotec Mitra Utama', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Macmahon Indonesia', 'kategori' => 'Kontraktor'],
            ['nama' => 'PT. Manado Karya Anugerah', 'kategori' => 'Kontraktor'],
            ['nama' => 'PT. Mandara Fasilitas Indonesia', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Maxidrill', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Metso Outotec', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Panca', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Pilar Muda Indotama', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. PSI Drilling Service', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Samudera Mulia Abadi', 'kategori' => 'Kontraktor'],
            ['nama' => 'PT. Saribuana Manado', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Tata Wisata', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Tombers Karya Bersama', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Tou Maesa Sejahtera', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. Trakindo', 'kategori' => 'Kontraktor'],
            ['nama' => 'PT. Tumou Tou Manado', 'kategori' => 'Subkontraktor'],
            ['nama' => 'PT. United Tractor', 'kategori' => 'Kontraktor'],
            ['nama' => 'Siloam Hospital', 'kategori' => 'Lainnya'],
            ['nama' => 'Visitor', 'kategori' => 'Lainnya'],
            ['nama' => 'PT. Meares Soputan Mining', 'kategori' => 'Pemegang IUP'],
            ['nama' => 'PT. Tambang Tondano Nusajaya', 'kategori' => 'Pemegang IUP'],
        ];

        foreach ($companies as $comp) {
            Perusahaan::firstOrCreate(
                ['nama_perusahaan' => $comp['nama']],
                ['kategori' => $comp['kategori'], 'is_active' => true]
            );
        }
    }
}
