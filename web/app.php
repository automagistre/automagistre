<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

$loader = require dirname(__DIR__).'/app/autoload.php';

if ($debug = filter_var(getenv('SYMFONY_DEBUG'), FILTER_VALIDATE_BOOLEAN)) {
    Debug::enable();
}

$kernel = new AppKernel(getenv('SYMFONY_ENV'), $debug);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
