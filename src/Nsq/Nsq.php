<?php

declare(strict_types=1);

namespace App\Nsq;

use function Amp\call;
use Amp\CancellationToken;
use Amp\CancellationTokenSource;
use Amp\Promise;
use function Amp\Promise\rethrow;
use Amp\Socket\EncryptableSocket;
use Amp\Socket\SocketPool;
use Amp\Socket\UnlimitedSocketPool;
use Generator;
use LogicException;
use PHPinnacle\Buffer\ByteBuffer;
use Sentry\SentryBundle\SentryBundle;
use function sprintf;
use Throwable;

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

    private SocketPool $pool;

    private array $config;

    public function __construct(?SocketPool $pool, array $config = [])
    {
        $this->pool = $pool ?? new UnlimitedSocketPool();
        $this->config = [
            'localAddr' => $config['localAddr'] ?? 'tcp://nsqd:4150',
        ];
    }

    /**
     * @psalm-return Promise<void>
     */
    public function pub(string $topic, string $message): Promise
    {
        return call(function () use ($topic, $message): Generator {
            /** @var EncryptableSocket $socket */
            $socket = yield $this->getSocket($topic);

            yield $socket->write(Command::pub($topic, $message));

            $buffer = yield $socket->read();

            // TODO clear to prevent sending Magic package twice. Refactor to WeakMap after migrate to php 8.0?
            $this->pool->clear($socket);

            if (null === $buffer) {
                throw new LogicException('NSQ return unexpected null.');
            }

            $buffer = new ByteBuffer($buffer);
            $size = $buffer->consumeUint32();
            $type = $buffer->consumeUint32();

            if (self::TYPE_ERROR === $type) {
                throw new LogicException(sprintf('NSQ return error: "%s"', $buffer->consume($size - self::BYTES_TYPE)));
            }

            if (self::TYPE_RESPONSE !== $type) {
                throw new LogicException(sprintf('Expecting "%s" type, but NSQ return: "%s"', self::TYPE_RESPONSE, $type));
            }

            $response = $buffer->consume($size - self::BYTES_TYPE);
            if (self::OK !== $response) {
                throw new LogicException(sprintf('NSQ return unexpected response: "%s"', $response));
            }
        });
    }

    public function subscribe(string $topic, string $channel, callable $callable): Stopper
    {
        $tokenSource = new CancellationTokenSource();

        $stopper = new Stopper(static function () use ($tokenSource): void {
            $tokenSource->cancel();
        });

        rethrow(call(function () use ($topic, $channel, $callable, $stopper, $tokenSource): Generator {
            $fragment = sprintf('%s-%s', $topic, $channel);
            /** @var EncryptableSocket $socket */
            $socket = yield $this->getSocket($fragment, $tokenSource->getToken());

            yield $socket->write(Command::sub($topic, $channel));

            $buffer = new ByteBuffer();
            while (!$stopper->isStopped()) {
                yield $socket->write(Command::rdy(1));

                $size = 4;
                $sizeRead = false;
                while ($buffer->size() < $size) {
                    $chunk = yield $socket->read();

                    if (null === $chunk && $stopper->isStopped()) {
                        break 2;
                    }

                    if (null === $chunk) {
                        $buffer->empty();
                        $this->pool->checkin($socket);

                        $socket = yield $this->getSocket($fragment, $tokenSource->getToken());

                        continue 2;
                    }

                    $buffer->append($chunk);

                    /** @phpstan-ignore-next-line */
                    if (false === $sizeRead && $buffer->size() >= self::BYTES_SIZE) {
                        $size = $buffer->consumeUint32();
                        $sizeRead = true;
                    }
                }

                $type = $buffer->consumeUint32();

                if (self::TYPE_RESPONSE === $type) {
                    $response = $buffer->consume($size - self::BYTES_TYPE);

                    if (self::OK === $response) {
                        continue;
                    }

                    if (self::HEARTBEAT === $response) {
                        yield $socket->write(Command::nop());

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
                    static function () use ($socket, $id, &$finished): Promise {
                        if ($finished) {
                            throw new LogicException('Can\'t ack, message already finished.');
                        }

                        $finished = true;

                        return call(static function () use ($socket, $id): Generator {
                            yield $socket->write(Command::fin($id));
                        });
                    },
                    static function (int $timeout) use ($socket, $id, &$finished): Promise {
                        if ($finished) {
                            throw new LogicException('Can\'t retry, message already finished.');
                        }

                        $finished = true;

                        return call(static function () use ($socket, $id, $timeout): Generator {
                            yield $socket->write(Command::req($id, $timeout));
                        });
                    },
                    static function () use ($socket, $id): Promise {
                        return call(static function () use ($socket, $id): Generator {
                            yield $socket->write(Command::touch($id));
                        });
                    },
                );

                try {
                    yield from $callable($message);
                } catch (Throwable $e) {
                    SentryBundle::getCurrentHub()->captureException($e);

                    $this->pool->checkin($socket);

                    throw $e;
                }
            }

            yield $socket->write(Command::cls());

            $this->pool->clear($socket);
        }));

        return $stopper;
    }

    /**
     * @psalm-return Promise<EncryptableSocket>
     */
    private function getSocket(string $fragment = null, CancellationToken $token = null): Promise
    {
        return call(
            function (?string $fragment, ?CancellationToken $token): Generator {
                $fragment = null === $fragment ? '' : '#'.$fragment;
                $uri = $this->config['localAddr'].$fragment;

                $socket = yield $this->pool->checkout($uri, null, $token);

                yield $socket->write(Command::magic());

                return $socket;
            },
            $fragment,
            $token
        );
    }
}
