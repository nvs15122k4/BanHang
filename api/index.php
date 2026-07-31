<?php

// Vercel Serverless Function Entry Point
// This file handles all incoming requests and forwards them to Laravel

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Set the correct working directory
chdir(__DIR__ . '/..');

define('LARAVEL_START', microtime(true));

// Load the Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle the request
/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(Request::capture());
