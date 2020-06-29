<?php

namespace App\Nsq;

use function array_map;
use function count;
use function implode;
use function pack;
use const PHP_EOL;
use Sentry\Util\JSON;
use function sprintf;
use function strlen;

class Command
{
    private const MAGIC_V2 = '  V2';

    public static function magic(): string
    {
        return self::MAGIC_V2;
    }

    /**
     * Update client metadata on the server and negotiate features.
     */
    public static function identify(array $arr): string
    {
        $body = Json::encode($arr, JSON_THROW_ON_ERROR | JSON_FORCE_OBJECT);
        $size = pack('N', strlen($body));

        return 'IDENTIFY '.PHP_EOL.$size.$body;
    }

    /**
     * Subscribe to a topic/channel.
     */
    public static function sub(string $topic, string $channel): string
    {
        return sprintf('SUB %s %s', $topic, $channel).PHP_EOL;
    }

    /**
     * Publish a message to a topic.
     */
    public static function pub(string $topic, string $body): string
    {
        $size = pack('N', strlen($body));

        return 'PUB '.$topic.PHP_EOL.$size.$body;
    }

    /**
     * Publish multiple messages to a topic (atomically).
     */
    public static function mpub(string $topic, array $bodies): string
    {
        $num = pack('N', count($bodies));

        $mb = implode('', array_map(static function ($body): string {
            return pack('N', strlen($body)).$body;
        }, $bodies));

        $size = pack('N', strlen($num.$mb));

        return 'MPUB '.$topic.PHP_EOL.$size.$num.$mb;
    }

    /**
     * Publish a deferred message to a topic.
     */
    public static function dpub(string $topic, int $deferTime, string $body): string
    {
        $size = pack('N', strlen($body));

        return sprintf('DPUB %s %s', $topic, $deferTime).PHP_EOL.$size.$body;
    }

    /**
     * Update RDY state (indicate you are ready to receive N messages).
     */
    public static function rdy(int $count): string
    {
        return 'RDY '.$count.PHP_EOL;
    }

    /**
     * Finish a message (indicate successful processing).
     */
    public static function fin(string $id): string
    {
        return 'FIN '.$id.PHP_EOL;
    }

    /**
     * Re-queue a message (indicate failure to process)
     * The re-queued message is placed at the tail of the queue, equivalent to having just published it,
     * but for various implementation specific reasons that behavior should not be explicitly relied upon and may change in the future.
     * Similarly, a message that is in-flight and times out behaves identically to an explicit REQ.
     */
    public static function req(string $id, int $timeout): string
    {
        return sprintf('REQ %s %s', $id, $timeout).PHP_EOL;
    }

    /**
     * Reset the timeout for an in-flight message.
     */
    public static function touch(string $id): string
    {
        return 'TOUCH '.$id.PHP_EOL;
    }

    /**
     * Cleanly close your connection (no more messages are sent).
     */
    public static function cls(): string
    {
        return 'CLS'.PHP_EOL;
    }

    /**
     * Response for heartbeat.
     */
    public static function nop(): string
    {
        return 'NOP'.PHP_EOL;
    }

    public static function auth(string $secret): string
    {
        $size = pack('N', strlen($secret));

        return 'AUTH'.PHP_EOL.$size.$secret;
    }
}
