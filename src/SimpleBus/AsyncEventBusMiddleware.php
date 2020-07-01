<?php

declare(strict_types=1);

namespace App\SimpleBus;

use function Amp\Promise\wait;
use App\Nsq\Nsq;
use App\Tenant\Tenant;
use function get_class;
use const JSON_UNESCAPED_SLASHES;
use const PHP_SAPI;
use Sentry\SentryBundle\SentryBundle;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use function sprintf;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

final class AsyncEventBusMiddleware implements MessageBusMiddleware
{
    private Nsq $nsq;

    private Tenant $tenant;

    private bool $debug;

    private SerializerInterface $serializer;

    public function __construct(Nsq $nsq, Tenant $tenant, bool $debug, SerializerInterface $serializer)
    {
        $this->nsq = $nsq;
        $this->tenant = $tenant;
        $this->debug = $debug;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($message, callable $next): void
    {
        if (!$this->debug && 'cli' !== PHP_SAPI) {
            $topic = sprintf('%s_events', $this->tenant->toIdentifier());

            $body = $this->serializer->serialize(
                [
                    'class' => get_class($message),
                    'body' => $message,
                ],
                'json',
                [
                    'json_encode_options' => JSON_UNESCAPED_SLASHES,
                ]
            );

            try {
                wait($this->nsq->pub($topic, $body));
            } catch (Throwable $e) {
                SentryBundle::getCurrentHub()->captureException($e);
            }
//            return;
        }

        $next($message);
    }
}
