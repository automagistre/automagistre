<?php

declare(strict_types=1);

namespace App\Nsq;

use function Amp\call;
use Amp\Promise;
use function Amp\Promise\rethrow;
use Amp\Socket\EncryptableSocket;
use Amp\Socket\SocketPool;
use Amp\Socket\UnlimitedSocketPool;
use Generator;
use LogicException;
use Sentry\SentryBundle\SentryBundle;
use function sprintf;
use function strlen;
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
            $socket = yield $this->pool->checkout(sprintf('%s#%s', $this->config['localAddr'], $topic));

            yield $socket->write(Command::magic());
            yield $socket->write(Command::pub($topic, $message));

            $buffer = yield $socket->read();

            $this->pool->checkin($socket);

            if (null === $buffer) {
                throw new LogicException('NSQ return unexpected null.');
            }

            $size = Extractor::int32($buffer, self::BYTES_SIZE);
            $type = Extractor::int32($buffer, self::BYTES_TYPE);

            if (self::TYPE_ERROR === $type) {
                throw new LogicException(sprintf('NSQ return error: "%s"', Extractor::string($buffer, $size)));
            }

            if (self::TYPE_RESPONSE !== $type) {
                throw new LogicException(sprintf('Expecting "%s" type, but NSQ return: "%s"', self::TYPE_RESPONSE, $type));
            }

            $response = Extractor::string($buffer, $size);
            if (self::OK !== $response) {
                throw new LogicException(sprintf('NSQ return unexpected response: "%s"', $response));
            }
        });
    }

    public function subscribe(string $topic, string $channel, callable $callable): void
    {
        rethrow(call(function () use ($topic, $channel, $callable): Generator {
            $uri = sprintf('%s#%s-%s', $this->config['localAddr'], $topic, $channel);

            /** @var EncryptableSocket $socket */
            $socket = yield $this->pool->checkout($uri);

            yield $socket->write(Command::magic());
            yield $socket->write(Command::sub($topic, $channel));

            $buffer = '';
            while (true) {
                yield $socket->write(Command::rdy(1));

                while (strlen($buffer) < 4) {
                    $buffer .= yield $socket->read();
                }

                $size = Extractor::int32($buffer, self::BYTES_SIZE);
                while (strlen($buffer) < $size) {
                    $buffer .= yield $socket->read();
                }

                $type = Extractor::int32($buffer, self::BYTES_TYPE);

                if (self::TYPE_RESPONSE === $type) {
                    $response = Extractor::string($buffer, $size - self::BYTES_TYPE);

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
                    throw new LogicException(Extractor::string($buffer, $size));
                }

                if (self::TYPE_MESSAGE !== $type) {
                    throw new LogicException(sprintf('Unsupported type: "%s"', $type));
                }

                $timestamp = Extractor::int64($buffer, self::BYTES_TIMESTAMP);
                $attempts = Extractor::uInt16($buffer, self::BYTES_ATTEMPTS);
                $id = Extractor::string($buffer, self::BYTES_ID);
                $body = Extractor::string($buffer, $size - self::BYTES_TYPE - self::BYTES_TIMESTAMP - self::BYTES_ATTEMPTS - self::BYTES_ID);

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

                        return call(static function () use ($socket, $id) {
                            yield $socket->write(Command::fin($id));
                        });
                    },
                    static function (int $timeout) use ($socket, $id, &$finished): Promise {
                        if ($finished) {
                            throw new LogicException('Can\'t retry, message already finished.');
                        }

                        $finished = true;

                        return call(static function () use ($socket, $id, $timeout) {
                            yield $socket->write(Command::req($id, $timeout));
                        });
                    },
                    static function () use ($socket, $id, &$finished): Promise {
                        if ($finished) {
                            throw new LogicException('Can\'t touch, message already finished.');
                        }

                        $finished = true;

                        return call(static function () use ($socket, $id) {
                            yield $socket->write(Command::touch($id));
                        });
                    },
                );

                try {
                    yield from $callable($message);
                } catch (Throwable $e) {
                    SentryBundle::getCurrentHub()->captureException($e);

                    throw $e;
                }
            }

            /** @phpstan-ignore-next-line */
            $this->pool->checkin($socket); // TODO Unreachable for now, call on graceful shutdown?
        }));
    }
}
