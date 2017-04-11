<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

$loader = require dirname(__DIR__).'/vendor/autoload.php';

if (class_exists(Dotenv::class) && file_exists($env = dirname(__DIR__).'/.env')) {
    (new Dotenv())->load($env);
}

if ($debug = filter_var(getenv('SYMFONY_DEBUG'), FILTER_VALIDATE_BOOLEAN)) {
    Debug::enable();
}

$kernel = new App\Kernel(getenv('SYMFONY_ENV'), $debug);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
