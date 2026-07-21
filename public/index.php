<?php

// First, let's get the path to the project root
$basePath = dirname(__DIR__);

// Register the Composer autoloader
require $basePath.'/vendor/autoload.php';

// Bootstrap the application and handle the request
$app = require_once $basePath.'/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
