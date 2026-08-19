<?php

namespace Database\Seeders;

use App\Models\Departemen;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds for 27 mining departments (prefixed with "Departemen ").
     */
    public function run(): void
    {
        $departments = [
            'Departemen Business Development',
            'Departemen Commercial',
            'Departemen Community Development',
            'Departemen Compliance',
            'Departemen Comrel & Land Acq',
            'Departemen Corporate Legal',
            'Departemen Environmental',
            'Departemen Exploration',
            'Departemen External Relations',
            'Departemen Fin & Acc Operational',
            'Departemen HCCS',
            'Departemen HSE & Formalities',
            'Departemen IT',
            'Departemen Maintenance',
            'Departemen Management',
            'Departemen Metallurgy',
            'Departemen Mining',
            'Departemen Mining Tech Service',
            'Departemen OHS',
            'Departemen Principal Mining',
            'Departemen Process Plant',
            'Departemen Project',
            'Departemen Resources & Reserve',
            'Departemen Security',
            'Departemen Supply Chain',
            'Departemen Sustainability & External Affairs',
            'Departemen Underground',
        ];

        foreach ($departments as $dept) {
            Departemen::firstOrCreate(
                ['nama_departemen' => $dept],
                ['is_active' => true]
            );
        }
    }
}
