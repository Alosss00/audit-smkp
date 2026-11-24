<?php

if (php_sapi_name() !== 'cli' || (function_exists('app') && app()->isProduction())) {
    http_response_code(403);
    exit('Forbidden: Script hanya dapat dijalankan melalui CLI di environment non-production.');
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/admin/audit-sesi/1/matrix', 'GET');
// We need auth though...
$user = App\Models\User::find(1);
$app['auth']->login($user);
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . PHP_EOL;
