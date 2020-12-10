<?php

declare(strict_types=1);

namespace App\Nsq;

use Generator;
use LogicException;

final class Subscriber
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function subscribe(string $topic, string $channel, ?float $timeout = 0): Generator
    {
        $reader = new Reader($this->config);
        $reader->sub($topic, $channel);
        $reader->rdy(1);

        while (true) {
            $message = $reader->consume($timeout);

            if (null === $message) {
                if (true === yield null) {
                    break;
                }

                continue;
            }

            $finished = false;
            $envelop = new Envelope(
                $message,
                static function () use ($reader, $message, &$finished): void {
                    if ($finished) {
                        throw new LogicException('Can\'t ack, message already finished.');
                    }

                    $finished = true;

                    $reader->fin($message->id);
                },
                static function (int $timeout) use ($reader, $message, &$finished): void {
                    if ($finished) {
                        throw new LogicException('Can\'t retry, message already finished.');
                    }

                    $finished = true;

                    $reader->req($message->id, $timeout);
                },
                static function () use ($reader, $message): void {
                    $reader->touch($message->id);
                },
            );

            if (true === yield $envelop) {
                break;
            }

            $reader->rdy(1);
        }

        $reader->close();
    }
}
