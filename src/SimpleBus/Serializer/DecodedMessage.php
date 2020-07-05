<?php

declare(strict_types=1);

namespace App\SimpleBus\Serializer;

use App\User\Entity\UserId;

/**
 * @psalm-immutable
 */
final class DecodedMessage
{
    public object $message;

    public string $trackingId;

    public UserId $userId;

    public function __construct(object $message, string $trackingId, UserId $userId)
    {
        $this->message = $message;
        $this->trackingId = $trackingId;
        $this->userId = $userId;
    }
}
