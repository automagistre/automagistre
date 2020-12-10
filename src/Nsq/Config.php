<?php

namespace App\Nsq;

/**
 * @psalm-immutable
 */
final class Config
{
    public string $nsqdAddress;

    public function __construct(string $nsqdAddress = 'tcp://nsqd:4150')
    {
        $this->nsqdAddress = $nsqdAddress;
    }
}
