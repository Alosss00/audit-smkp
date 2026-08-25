<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Kriteria;
use Illuminate\Support\Facades\DB;

$affected = DB::table('kriterias')->update(['persyaratan_dokumen' => null]);

echo "Successfully cleared persyaratan_dokumen data for {$affected} kriteria records.\n";
