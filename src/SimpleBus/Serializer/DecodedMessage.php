<?php

declare(strict_types=1);

namespace App\SimpleBus\Serializer;

/**
 * @psalm-immutable
 */
final class DecodedMessage
{
    public object $message;

    public string $trackingId;

    public function __construct(object $message, string $trackingId)
    {
        $this->message = $message;
        $this->trackingId = $trackingId;
    }
}
