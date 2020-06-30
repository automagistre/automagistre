<?php

declare(strict_types=1);

namespace App\Nsq;

use Amp\Promise;
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
    private $touch;

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
        $this->touch = $touch;
    }

    /**
     * @psalm-return Promise<void>
     */
    public function ack(): Promise
    {
        return call_user_func($this->acknowledge);
    }

    /**
     * @psalm-return Promise<void>
     */
    public function retry(int $timeout): Promise
    {
        return call_user_func($this->requeue, $timeout);
    }

    /**
     * @psalm-return Promise<void>
     */
    public function touch(): Promise
    {
        return call_user_func($this->touch);
    }
}
