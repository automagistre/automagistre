<?php

namespace App\MessageBus;

trait PrivateMessageRecorderCapabilities
{
    private array $messages = [];

    /**
     * @return object[]
     */
    public function eraseMessages(): array
    {
        [$messages, $this->messages] = [$this->messages, []];

        return $messages;
    }

    protected function record(object $message): void
    {
        $this->messages[] = $message;
    }
}
