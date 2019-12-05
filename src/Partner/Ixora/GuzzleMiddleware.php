<?php

declare(strict_types=1);

namespace App\Partner\Ixora;

use Closure;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class GuzzleMiddleware
{
    public static function authQuery(string $authCode): Closure
    {
        return Closure::fromCallable(Middleware::mapRequest(
            fn (RequestInterface $request): RequestInterface => $request->withUri(
                Uri::withQueryValue($request->getUri(), 'AuthCode', $authCode)
            )
        ));
    }

    public static function logErrors(LoggerInterface $logger): Closure
    {
        return Closure::fromCallable(Middleware::mapResponse(static function (Response $response) use ($logger): Response {
            if (200 !== $response->getStatusCode()) {
                $logger->alert('IXORA ERROR: '.$response->getBody()->getContents());
            }

            return $response;
        }));
    }
}
