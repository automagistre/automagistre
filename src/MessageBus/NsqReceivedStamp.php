<?php

declare(strict_types=1);

namespace App\MessageBus;

use App\Nsq\Envelope;
use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * @psalm-immutable
 */
final class NsqReceivedStamp implements StampInterface
{
    public Envelope $envelope;

    public function __construct(Envelope $envelope)
    {
        $this->envelope = $envelope;
    }
}
