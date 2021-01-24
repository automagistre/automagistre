<?php

declare(strict_types=1);

namespace App\MessageBus;

interface ContainsRecordedMessages
{
    /**
     * @return object[]
     */
    public function eraseMessages(): array;
}
