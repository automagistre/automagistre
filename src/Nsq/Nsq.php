<?php

declare(strict_types=1);

namespace App\Nsq;

use Generator;
use LogicException;
use PHPinnacle\Buffer\ByteBuffer;
use Socket\Raw\Factory;
use Socket\Raw\Socket;
use function sprintf;

final class Nsq
{
    private const OK = 'OK';
    private const HEARTBEAT = '_heartbeat_';
    private const TYPE_RESPONSE = 0;
    private const TYPE_ERROR = 1;
    private const TYPE_MESSAGE = 2;
    private const BYTES_SIZE = 4;
    private const BYTES_TYPE = 4;
    private const BYTES_TIMESTAMP = 8;
    private const BYTES_ATTEMPTS = 2;
    private const BYTES_ID = 16;

    private ?Socket $socket = null;

    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = [
            'localAddr' => $config['localAddr'] ?? 'tcp://nsqd:4150',
        ];
    }

    public function pub(string $topic, string $message): void
    {
        $socket = $this->getSocket();

        $socket->write(Command::pub($topic, $message));

        $buffer = new ByteBuffer($socket->read(self::BYTES_SIZE + self::BYTES_TYPE));
        $size = $buffer->consumeUint32();
        $type = $buffer->consumeUint32();

        $response = $socket->read($size);

        if (self::TYPE_ERROR === $type) {
            throw new LogicException(sprintf('NSQ return error: "%s"', $response));
        }

        if (self::TYPE_RESPONSE !== $type) {
            throw new LogicException(sprintf('Expecting "%s" type, but NSQ return: "%s"', self::TYPE_RESPONSE, $type));
        }

        if (self::OK !== $response) {
            throw new LogicException(sprintf('NSQ return unexpected response: "%s"', $response));
        }
    }

    public function subscribe(string $topic, string $channel, float $timeout = null): Generator
    {
        $socket = $this->getSocket();
        $socket->write(Command::sub($topic, $channel));

        $buffer = new ByteBuffer();
        while (true) {
            $socket->write(Command::rdy(1));

            if (false === $socket->selectRead($timeout)) {
                if (true === yield null) {
                    break;
                }

                continue;
            }

            $buffer->append($socket->read(self::BYTES_SIZE));
            $size = $buffer->consumeUint32();

            $buffer->append($socket->read($size));
            $type = $buffer->consumeUint32();

            if (self::TYPE_RESPONSE === $type) {
                $response = $buffer->consume($size - self::BYTES_TYPE);

                if (self::OK === $response) {
                    continue;
                }

                if (self::HEARTBEAT === $response) {
                    $socket->write(Command::nop());

                    continue;
                }

                throw new LogicException(sprintf('Unsupported response: "%s"', $response));
            }

            if (self::TYPE_ERROR === $type) {
                throw new LogicException($buffer->consume($size - self::BYTES_TYPE));
            }

            if (self::TYPE_MESSAGE !== $type) {
                throw new LogicException(sprintf('Unsupported type: "%s"', $type));
            }

            $timestamp = $buffer->consumeInt64();
            $attempts = $buffer->consumeUint16();
            $id = $buffer->consume(self::BYTES_ID);
            $body = $buffer->consume($size - self::BYTES_TYPE - self::BYTES_TIMESTAMP - self::BYTES_ATTEMPTS - self::BYTES_ID);

            $finished = false;
            $message = new Envelop(
                $timestamp,
                $attempts,
                $id,
                $body,
                static function () use ($socket, $id, &$finished): void {
                    if ($finished) {
                        throw new LogicException('Can\'t ack, message already finished.');
                    }

                    $finished = true;

                    $socket->write(Command::fin($id));
                },
                static function (int $timeout) use ($socket, $id, &$finished): void {
                    if ($finished) {
                        throw new LogicException('Can\'t retry, message already finished.');
                    }

                    $finished = true;

                    $socket->write(Command::req($id, $timeout));
                },
                static function () use ($socket, $id): void {
                    $socket->write(Command::touch($id));
                },
            );

            if (true === yield $message) {
                break;
            }
        }

        $socket->write(Command::cls());
    }

    private function getSocket(): Socket
    {
        if (null !== $this->socket) {
            return $this->socket;
        }

        $socket = (new Factory())->createClient($this->config['localAddr']);
        $socket->write(Command::magic());

        return $this->socket = $socket;
    }
}
