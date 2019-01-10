<?php

declare(strict_types=1);

\ini_set('display_errors', 'stderr');

use App\Kernel;
use App\SymfonyClient;
use Spiral\Debug as SpiralDebug;
use Spiral\Goridge\StreamRelay;
use Spiral\RoadRunner\Worker;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

require \dirname(__DIR__).'/vendor/autoload.php';

if (\class_exists(Dotenv::class) && \file_exists($env = \dirname(__DIR__).'/.env')) {
    (new Dotenv())->load($env);
}

if ($debug = \filter_var(\getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN)) {
    \umask(0000);

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

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(\explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts(\explode(',', $trustedHosts));
}

$kernel = new Kernel(\getenv('APP_ENV'), $debug);
$kernel->boot();

$client = new SymfonyClient(new Worker(new StreamRelay(STDIN, STDOUT)));
$session = $kernel->getContainer()->has('session') ? $kernel->getContainer()->get('session') : null;
$cookieOptions = $kernel->getContainer()->getParameter('session.storage.options');
$cookieFactory = function (Request $request) use ($session, $cookieOptions) {
    return new \Symfony\Component\HttpFoundation\Cookie(
        $session->getName(),
        $session->getId(),
        $cookieOptions['cookie_lifetime'] ?? 0,
        $cookieOptions['cookie_path'] ?? '/',
        $cookieOptions['cookie_domain'] ?? '',
        ($cookieOptions['cookie_secure'] ?? 'auto') === 'auto'
            ? $request->isSecure() : (bool) ($cookieOptions['cookie_secure'] ?? 'auto'),
        $cookieOptions['cookie_httponly'] ?? true,
        false,
        $cookieOptions['cookie_samesite'] ?? null
    );
};

while ($request = $client->acceptRequest()) {
    try {
        $session->setId($request->cookies->get($session->getName(), ''));

        $response = $kernel->handle($request);

        if (!\in_array($session->getId(), ['', $request->cookies->get($session->getName())], true)) {
            $response->headers->setCookie($cookieFactory($request));
        }

        $client->respond($response);
        $kernel->terminate($request, $response);
    } catch (\Throwable $e) {
        $client->error((string) $e);
    } finally {
        if ($session->isStarted()) {
            $session->save();
        }

        $kernel->getContainer()->get('session.memcached')->quit();
        \session_unset();
    }
}
