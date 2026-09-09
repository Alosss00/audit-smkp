<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

foreach (['elemens', 'sub_elemens', 'kriterias', 'audit_sesis', 'perusahaans', 'departemens', 'users'] as $t) {
    $has = \Illuminate\Support\Facades\Schema::hasColumn($t, 'deleted_at');
    echo "$t: " . ($has ? "YES" : "NO") . "\n";
}
