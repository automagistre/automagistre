<?php

declare(strict_types=1);

namespace App\RoadRunner;

use Error;
use LogicException;
use Spiral\Goridge\StreamRelay;
use Spiral\RoadRunner\Worker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SymfonyClient
{
    /**
     * @var Worker
     */
    private $worker;

    public function __construct(Worker $worker = null)
    {
        $this->worker = $worker ?? new Worker(new StreamRelay(STDIN, STDOUT));
    }

    public function acceptRequest(): ?Request
    {
        $body = $this->worker->receive($ctx);
        if ($body instanceof Error) {
            throw $body;
        }

        if (null === $body && null === $ctx) {
            // termination request
            return null;
        }

        $ctx = \json_decode($ctx, true);
        if (false === $ctx) {
            throw new \RuntimeException('invalid context');
        }

        $parameters = [];
        $content = null;
        if ($ctx['parsed']) {
            $parameters = \json_decode($body, true);
        } elseif (null !== $body) {
            $content = $body;
        }

        $server = $this->configureServer($ctx);
        $headers = \array_map(function (array $header) {
            return $header[0];
        }, $ctx['headers']);

        if (null !== $ctx['uploads']) {
            throw new LogicException('Not implemented yet.');
        }

        $request = Request::create($ctx['uri'], $ctx['method'], $parameters, $ctx['cookies'], [], $server, $content);
        $request->headers->replace($headers);

        return $request;
    }

    public function respond(Response $response): void
    {
        $headers = $response->headers->all();
        if ([] === $headers) {
            // this is required to represent empty header set as map and not as array
            $headers = new \stdClass();
        }

        $header = \json_encode([
            'status' => $response->getStatusCode(),
            'headers' => $headers,
        ]);
        if (false === $header) {
            throw new LogicException('Can\'t json_encode headers');
        }

        $this->worker->send($response->getContent(), $header);
    }

    public function error(string $message): void
    {
        $this->worker->error($message);
    }

    /**
     * Returns altered copy of _SERVER variable. Sets ip-address, request-time and other values.
     */
    protected function configureServer(array $ctx): array
    {
        $server = $_SERVER;
        $server['REQUEST_TIME'] = \time();
        $server['REQUEST_TIME_FLOAT'] = \microtime(true);
        $server['REMOTE_ADDR'] = $ctx['attributes']['ipAddress'] ?? $ctx['remoteAddr'] ?? '127.0.0.1';
        $server['HTTP_USER_AGENT'] = $ctx['headers']['User-Agent'][0] ?? '';

        return $server;
    }
}
