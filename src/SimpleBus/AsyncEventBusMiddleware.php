<?php

declare(strict_types=1);

namespace App\SimpleBus;

use Amp\Loop;
use function Amp\Promise\wait;
use App\Nsq\Nsq;
use App\SimpleBus\Serializer\DecodedMessage;
use App\SimpleBus\Serializer\MessageSerializer;
use App\Tenant\Tenant;
use App\User\Entity\User;
use function assert;
use Ramsey\Uuid\Uuid;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use Symfony\Component\Security\Core\Security;

final class AsyncEventBusMiddleware implements MessageBusMiddleware
{
    private Nsq $nsq;

    private Tenant $tenant;

    private MessageSerializer $serializer;

    private ?string $handlingId = null;

    private Security $security;

    public function __construct(Nsq $nsq, Tenant $tenant, MessageSerializer $serializer, Security $security)
    {
        $this->nsq = $nsq;
        $this->tenant = $tenant;
        $this->serializer = $serializer;
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($message, callable $next): void
    {
        [$message, $trackingId, $envelop] = $message instanceof DecodedMessage
            ? [$message->message, $message->trackingId, $message]
            : [$message, Uuid::uuid6()->toString(), null];

        if (null !== $envelop && null === $this->handlingId) {
            $this->handlingId = $trackingId;

            try {
                $next($message);

                return;
            } finally {
                $this->handlingId = null;
            }
        }

        $user = $this->security->getUser();
        assert($user instanceof User);

        $promise = $this->nsq->pub(
            $this->tenant->toBusTopic(),
            $this->serializer->encode(
                $this->handlingId ?? $trackingId,
                $message,
                $user->toId(),
            )
        );

        if (true === Loop::getInfo()['running']) {
            Loop::defer(fn () => yield $promise);
        } else {
            wait($promise);
        }
    }
}
