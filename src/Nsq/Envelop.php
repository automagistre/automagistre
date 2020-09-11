<?php

declare(strict_types=1);

namespace App\Nsq;

use function call_user_func;

/**
 * @psalm-immutable
 */
final class Envelop
{
    public int $timestamp;

    public int $attempts;

    public string $id;

    public string $body;

    /**
     * @var callable
     */
    private $acknowledge;

    /**
     * @var callable
     */
    private $requeue;

    /**
     * @var callable
     */
    private $touching;

    public function __construct(
        int $timestamp,
        int $attempts,
        string $id,
        string $body,
        callable $ack,
        callable $req,
        callable $touch
    ) {
        $this->timestamp = $timestamp;
        $this->attempts = $attempts;
        $this->id = $id;
        $this->body = $body;
        $this->acknowledge = $ack;
        $this->requeue = $req;
        $this->touching = $touch;
    }

    public function ack(): void
    {
        call_user_func($this->acknowledge);
    }

    public function retry(int $timeout): void
    {
        call_user_func($this->requeue, $timeout);
    }

    public function touch(): void
    {
        call_user_func($this->touching);
    }
}
