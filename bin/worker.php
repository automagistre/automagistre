<?php

declare(strict_types=1);

\define('RR_WORKER', true);

\ini_set('display_errors', 'stderr');

use App\Kernel;
use App\RoadRunner\SymfonyClient;
use Spiral\Debug as SpiralDebug;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

require \dirname(__DIR__).'/vendor/autoload.php';

if (\class_exists(Dotenv::class) && \file_exists($env = \dirname(__DIR__).'/.env')) {
    (new Dotenv())->load($env);
}

if ($debug = \filter_var(\getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN)) {
    \umask(0000);

    Debug::enable();
}

function sdump($var)
{
    static $dumper = null;
    if (null === $dumper) {
        $dumper = new SpiralDebug\Dumper();
        $dumper->setRenderer(SpiralDebug\Dumper::ERROR_LOG, new SpiralDebug\Renderer\ConsoleRenderer());
    }

    $dumper->dump($var, SpiralDebug\Dumper::ERROR_LOG);

    return $var;
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(\explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts(\explode(',', $trustedHosts));
}

$kernel = new Kernel(\getenv('APP_ENV'), $debug);
$kernel->boot();

$client = new SymfonyClient();

while ($request = $client->acceptRequest()) {
    try {
        $request->overrideGlobals();
        $response = $kernel->handle($request);
        $client->respond($response);
        $kernel->terminate($request, $response);
    } catch (\Throwable $e) {
        $kernel->getContainer()->get('sentry.client')->captureException($e);

        $referer = $request->query->get('referer') ?? $request->headers->get('referer');
        $client->respond(new RedirectResponse('/_rr_error?referer='.\urlencode($referer)));

        exit(1);
    }
}
