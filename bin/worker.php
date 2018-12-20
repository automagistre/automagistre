<?php

declare(strict_types=1);

\ini_set('display_errors', 'stderr');

use App\Kernel;
use Spiral\Debug as SpiralDebug;
use Spiral\Goridge\StreamRelay;
use Spiral\RoadRunner\PSR7Client;
use Spiral\RoadRunner\Worker;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

require \dirname(__DIR__).'/vendor/autoload.php';

if (\class_exists(Dotenv::class) && \file_exists($env = \dirname(__DIR__).'/.env')) {
    (new Dotenv())->load($env);
}

if ($debug = \filter_var(\getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN)) {
    \umask(0000);

    $dumper = new SpiralDebug\Dumper();
    $dumper->setRenderer(SpiralDebug\Dumper::ERROR_LOG, new SpiralDebug\Renderer\ConsoleRenderer());

    $dump = function ($var) use ($dumper) {
        $dumper->dump($var, SpiralDebug\Dumper::ERROR_LOG);

        return $var;
    };

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
$relay = new StreamRelay(STDIN, STDOUT);
//$relay = new SocketRelay('/tmp/rr.sock', null, SocketRelay::SOCK_UNIX);
$psr7 = new PSR7Client(new Worker($relay));
$httpFoundationFactory = new HttpFoundationFactory();
$diactorosFactory = new DiactorosFactory();

while ($req = $psr7->acceptRequest()) {
    try {
        $request = $httpFoundationFactory->createRequest($req);

        $sessionId = null;
        if ($request->cookies->has(\session_name())) {
            $sessionId = $request->cookies->get(\session_name());
            \session_id($sessionId);
            $kernel->getContainer()->get('session')->setId($sessionId);
        }

        $response = $kernel->handle($request);

        if (
            $request->hasSession()
            && $sessionId !== $request->getSession()->getId()
            && [] !== $request->getSession()->all()
        ) {
            $cookie_options = $kernel->getContainer()->getParameter('session.storage.options');
            $response->headers->setCookie(
                new \Symfony\Component\HttpFoundation\Cookie(
                    \session_name(),
                    \session_id(),
                    $cookie_options['cookie_lifetime'] ?? 0,
                    $cookie_options['cookie_path'] ?? '/',
                    $cookie_options['cookie_domain'] ?? '',
                    ($cookie_options['cookie_secure'] ?? 'auto') === 'auto' ? $request->isSecure() : (bool) ($cookie_options['cookie_secure'] ?? 'auto'),
                    $cookie_options['cookie_httponly'] ?? true,
                    false,
                    $cookie_options['cookie_samesite'] ?? null
                )
            );
        }

        $psr7->respond($diactorosFactory->createResponse($response));
        $kernel->terminate($request, $response);
        $kernel->reboot($kernel->getCacheDir());
    } catch (\Throwable $e) {
        $psr7->getWorker()->error((string) $e);
    } finally {
        if (PHP_SESSION_ACTIVE === \session_status()) {
            \session_write_close();

            \session_id('');
            \session_unset();
            unset($_SESSION);
        }
    }
}
