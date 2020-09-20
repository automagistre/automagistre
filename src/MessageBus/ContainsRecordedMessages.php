<?php

namespace App\MessageBus;

interface ContainsRecordedMessages
{
    /**
     * @return object[]
     */
    public function eraseMessages(): array;
}
