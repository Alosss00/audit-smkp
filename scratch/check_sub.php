<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$subElemensWithoutKriteria = App\Models\SubElemen::doesntHave('kriterias')->get();
echo "Total SubElemen without Kriteria: " . $subElemensWithoutKriteria->count() . "\n";
foreach ($subElemensWithoutKriteria as $sub) {
    echo "ID: {$sub->id} | Kode: {$sub->kode_sub} | Nama: {$sub->nama_sub}\n";
}
