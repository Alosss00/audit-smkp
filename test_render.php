<?php
require 'C:/laragon/www/audit/vendor/autoload.php';
$app = require_once 'C:/laragon/www/audit/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/admin/audit-sesi/1/matrix', 'GET');
// We need auth though...
$user = App\Models\User::find(1);
$app['auth']->login($user);
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . PHP_EOL;
