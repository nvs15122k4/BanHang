<?php

// Vercel Serverless Function Entry Point
// This file handles all incoming requests and forwards them to Laravel

// Set the correct working directory
chdir(__DIR__ . '/..');

// Load the Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle the request
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
