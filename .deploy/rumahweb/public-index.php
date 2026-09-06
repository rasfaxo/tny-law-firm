<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$applicationPath = dirname(__DIR__).'/tny-law-firm';

if (! is_file($applicationPath.'/vendor/autoload.php') || ! is_file($applicationPath.'/bootstrap/app.php')) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Aplikasi sedang dipersiapkan. Silakan coba kembali beberapa saat lagi.');
}

if (file_exists($maintenance = $applicationPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $applicationPath.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $applicationPath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
