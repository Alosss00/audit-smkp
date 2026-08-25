<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SubElemen;

$subElemens = SubElemen::all();
foreach ($subElemens as $sub) {
    $sub->redistributeKriteriaScores();
}

echo "Score redistribution completed for " . $subElemens->count() . " SubElemen records.\n";
