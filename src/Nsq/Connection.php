<?php

namespace App\Nsq;

use function array_map;
use function count;
use function implode;
use LogicException;
use function pack;
use const PHP_EOL;
use PHPinnacle\Buffer\ByteBuffer;
use Sentry\Util\JSON;
use Socket\Raw\Factory;
use Socket\Raw\Socket;
use function sprintf;
use function strlen;
use Throwable;

class Connection
{
    public const OK = 'OK';
    public const HEARTBEAT = '_heartbeat_';
    public const TYPE_RESPONSE = 0;
    public const TYPE_ERROR = 1;
    public const TYPE_MESSAGE = 2;
    public const BYTES_SIZE = 4;
    public const BYTES_TYPE = 4;
    public const BYTES_ATTEMPTS = 2;
    public const BYTES_TIMESTAMP = 8;
    public const BYTES_ID = 16;
    private const MAGIC_V2 = '  V2';

    private Socket $socket;

    private bool $closed = false;

    private function __construct(Socket $socket)
    {
        $this->socket = $socket;
    }

    public function __destruct()
    {
        try {
            $this->socket->close();
        } catch (Throwable $e) {
        }
    }

    public static function connect(Config $config): self
    {
        $socket = (new Factory())->createClient($config->nsqdAddress);
        $socket->write(self::MAGIC_V2);

        return new self($socket);
    }

    /**
     * Update client metadata on the server and negotiate features.
     */
    public function identify(array $arr): string
    {
        $body = Json::encode($arr, JSON_THROW_ON_ERROR | JSON_FORCE_OBJECT);
        $size = pack('N', strlen($body));

        return 'IDENTIFY '.PHP_EOL.$size.$body;
    }

    /**
     * Subscribe to a topic/channel.
     */
    public function sub(string $topic, string $channel): void
    {
        $buffer = sprintf('SUB %s %s', $topic, $channel).PHP_EOL;

        $this->write($buffer, true);
    }

    /**
     * Publish a message to a topic.
     */
    public function pub(string $topic, string $body): void
    {
        $size = pack('N', strlen($body));

        $buffer = 'PUB '.$topic.PHP_EOL.$size.$body;

        $this->write($buffer, true);
    }

    /**
     * Publish multiple messages to a topic (atomically).
     */
    public function mpub(string $topic, array $bodies): void
    {
        $num = pack('N', count($bodies));

        $mb = implode('', array_map(static function ($body): string {
            return pack('N', strlen($body)).$body;
        }, $bodies));

        $size = pack('N', strlen($num.$mb));

        $buffer = 'MPUB '.$topic.PHP_EOL.$size.$num.$mb;

        $this->write($buffer, true);
    }

    /**
     * Publish a deferred message to a topic.
     */
    public function dpub(string $topic, int $deferTime, string $body): void
    {
        $size = pack('N', strlen($body));

        $buffer = sprintf('DPUB %s %s', $topic, $deferTime).PHP_EOL.$size.$body;

        $this->write($buffer, true);
    }

    /**
     * Update RDY state (indicate you are ready to receive N messages).
     */
    public function rdy(int $count): void
    {
        $this->write('RDY '.$count.PHP_EOL, false);
    }

    /**
     * Finish a message (indicate successful processing).
     */
    public function fin(string $id): void
    {
        $this->write('FIN '.$id.PHP_EOL, false);
    }

    /**
     * Re-queue a message (indicate failure to process)
     * The re-queued message is placed at the tail of the queue, equivalent to having just published it,
     * but for various implementation specific reasons that behavior should not be explicitly relied upon and may change in the future.
     * Similarly, a message that is in-flight and times out behaves identically to an explicit REQ.
     */
    public function req(string $id, int $timeout): void
    {
        $this->write(sprintf('REQ %s %s', $id, $timeout).PHP_EOL, false);
    }

    /**
     * Reset the timeout for an in-flight message.
     */
    public function touch(string $id): void
    {
        $this->write('TOUCH '.$id.PHP_EOL, false);
    }

    /**
     * Cleanly close your connection (no more messages are sent).
     */
    public function cls(): void
    {
        $this->write('CLS'.PHP_EOL, true);
    }

    public function auth(string $secret): string
    {
        $size = pack('N', strlen($secret));

        return 'AUTH'.PHP_EOL.$size.$secret;
    }

    public function consume(?float $timeout = null): ?Message
    {
        if (false === $this->socket->selectRead($timeout)) {
            return null;
        }

        return $this->read() ?? $this->consume(0);
    }

    private function write(string $buffer, bool $hasResponse): void
    {
        if ($this->closed) {
            throw new LogicException('This connection is closed, create new one.');
        }

        try {
            $this->socket->write($buffer);

            if ($hasResponse) {
                $this->read();
            }
        } catch (Throwable $e) {
            $this->closed = true;

            throw $e;
        }
    }

    private function read(): ?Message
    {
        $socket = $this->socket;

        $buffer = new ByteBuffer($socket->read(self::BYTES_SIZE + self::BYTES_TYPE));
        $size = $buffer->consumeUint32();
        $type = $buffer->consumeUint32();

        $buffer->append($socket->read($size - self::BYTES_TYPE));

        if (self::TYPE_RESPONSE === $type) {
            $response = $buffer->consume($size - self::BYTES_TYPE);

            if (self::OK === $response) {
                return null;
            }

            if (self::HEARTBEAT === $response) {
                $socket->write('NOP'.PHP_EOL);

                return null;
            }

            throw new LogicException(sprintf('Unexpected response from nsq: "%s"', $response));
        }

        if (self::TYPE_ERROR === $type) {
            throw new LogicException(sprintf('NSQ return error: "%s"', $socket->read($size)));
        }

        if (self::TYPE_MESSAGE !== $type) {
            throw new LogicException(sprintf('Expecting "%s" type, but NSQ return: "%s"', self::TYPE_MESSAGE, $type));
        }

        $timestamp = $buffer->consumeInt64();
        $attempts = $buffer->consumeUint16();
        $id = $buffer->consume(self::BYTES_ID);
        $body = $buffer->consume($size - self::BYTES_TYPE - self::BYTES_TIMESTAMP - self::BYTES_ATTEMPTS - self::BYTES_ID);

        return new Message($timestamp, $attempts, $id, $body);
    }
}
