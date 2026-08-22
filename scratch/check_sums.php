<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\Elemen::with('subElemens.kriterias')->orderBy('kode_elemen')->get() as $e) {
    $sum = 0;
    foreach ($e->subElemens as $sub) {
        $sum += $sub->kriterias->sum('nilai_maksimal');
    }
    echo "Elemen {$e->kode_elemen} ({$e->nama_elemen}): Sum = {$sum}\n";
}

echo "Total Max Score Overall: " . App\Models\Kriteria::sum('nilai_maksimal') . "\n";
