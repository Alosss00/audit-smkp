<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Login as admin for testing
$user = App\Models\User::where('role', 'admin')->first();
auth()->login($user);

echo "1. Testing Admin Dashboard view...\n";
try {
    $view = app(\App\Http\Controllers\DashboardController::class)->admin();
    echo "Dashboard Render Success! Length: " . strlen($view->render()) . "\n";
} catch (\Throwable $e) {
    echo "Dashboard Render Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}

echo "\n2. Testing Monitoring Audit index view...\n";
try {
    $req = new \Illuminate\Http\Request();
    $view = app(\App\Http\Controllers\Admin\AuditOversightController::class)->index($req);
    echo "Monitoring Index Render Success! Length: " . strlen($view->render()) . "\n";
} catch (\Throwable $e) {
    echo "Monitoring Index Render Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}

echo "\n3. Testing Laporan Detail view...\n";
try {
    $sesi = App\Models\AuditSesi::first();
    $view = app(\App\Http\Controllers\Admin\AuditSesiAdminController::class)->laporanDetail($sesi->id);
    echo "Laporan Detail Render Success! Length: " . strlen($view->render()) . "\n";
} catch (\Throwable $e) {
    echo "Laporan Detail Render Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
