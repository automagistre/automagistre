<?php

declare(strict_types=1);

namespace App\Nsq;

use Generator;
use LogicException;

final class Consumer
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function subscribe(string $topic, string $channel, ?float $timeout = 0): Generator
    {
        $connection = Connection::connect($this->config);
        $connection->sub($topic, $channel);
        $connection->rdy(1);

        while (true) {
            $message = $connection->consume($timeout);

            if (null === $message) {
                if (true === yield null) {
                    break;
                }

                continue;
            }

            $finished = false;
            $envelop = new Envelope(
                $message,
                static function () use ($connection, $message, &$finished): void {
                    if ($finished) {
                        throw new LogicException('Can\'t ack, message already finished.');
                    }

                    $finished = true;

                    $connection->fin($message->id);
                },
                static function (int $timeout) use ($connection, $message, &$finished): void {
                    if ($finished) {
                        throw new LogicException('Can\'t retry, message already finished.');
                    }

                    $finished = true;

                    $connection->req($message->id, $timeout);
                },
                static function () use ($connection, $message): void {
                    $connection->touch($message->id);
                },
            );

            if (true === yield $envelop) {
                break;
            }

            $connection->rdy(1);
        }

        $connection->cls();
    }
}
