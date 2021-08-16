<?php

declare(strict_types=1);

namespace App\Tenant\Messenger;

use App\Tenant\State;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ConsumedByWorkerStamp;

final class TenantMiddleware implements MiddlewareInterface
{
    public function __construct(private State $state)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $previous = $this->state->tenant;

        if (
            null !== $previous
            && (null === $envelope->last(ConsumedByWorkerStamp::class)
            || null === $envelope->last(TenantStamp::class))
        ) {
            return $stack->next()->handle($envelope->with(new TenantStamp($previous)), $stack);
        }

        /** @var null|TenantStamp $stamp */
        $stamp = $envelope->last(TenantStamp::class);

        $this->state->set($stamp?->getTenant());

        try {
            return $stack->next()->handle($envelope, $stack);
        } finally {
            $this->state->set($previous);
        }
    }
}
