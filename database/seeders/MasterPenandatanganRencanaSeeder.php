<?php

namespace Database\Seeders;

use App\Models\MasterPenandatanganRencana;
use Illuminate\Database\Seeder;

class MasterPenandatanganRencanaSeeder extends Seeder
{
    /**
     * Seed signatory matrix per business group (slide 37 materi pelatihan SMKP).
     */
    public function run(): void
    {
        $matrix = [
            'jasa_pertambangan' => [
                1 => 'Ketua Tim',
                2 => 'PJO',
                3 => 'KTT atau PTL',
                4 => 'KaIT',
            ],
            'tambang' => [
                1 => 'Ketua Tim',
                2 => 'KTT',
                3 => 'KaIT',
            ],
            'pengolahan_pemurnian' => [
                1 => 'Ketua Tim',
                2 => 'PTL',
                3 => 'KaIT',
            ],
        ];

        foreach ($matrix as $kelompok => $jabatans) {
            foreach ($jabatans as $urutan => $jabatan) {
                MasterPenandatanganRencana::updateOrCreate(
                    ['kelompok' => $kelompok, 'urutan' => $urutan],
                    ['jabatan' => $jabatan, 'is_active' => true]
                );
            }
        }
    }
}
