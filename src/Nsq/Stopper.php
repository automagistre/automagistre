<?php

declare(strict_types=1);

namespace App\Nsq;

use function call_user_func;

final class Stopper
{
    private bool $stopped = false;

    /**
     * @var callable
     */
    private $onStopped;

    public function __construct(callable $onStopped)
    {
        $this->onStopped = $onStopped;
    }

    public function stop(): void
    {
        if ($this->isStopped()) {
            return;
        }

        $this->stopped = true;

        call_user_func($this->onStopped);
    }

    public function isStopped(): bool
    {
        return $this->stopped;
    }
}
