<?php

namespace App\Nsq;

use function array_map;
use function count;
use function implode;
use function pack;
use const PHP_EOL;
use function sprintf;
use function strlen;

final class Writer
{
    private Connection $connection;

    public function __construct(Config $config)
    {
        $this->connection = Connection::connect($config);
    }

    /**
     * @psalm-suppress PossiblyFalseOperand
     */
    public function pub(string $topic, string $body): void
    {
        $size = pack('N', strlen($body));

        $buffer = 'PUB '.$topic.PHP_EOL.$size.$body;

        $this->connection->write($buffer);
        $this->connection->read();
    }

    /**
     * @psalm-suppress PossiblyFalseOperand
     */
    public function mpub(string $topic, array $bodies): void
    {
        $num = pack('N', count($bodies));

        $mb = implode('', array_map(static function ($body): string {
            return pack('N', strlen($body)).$body;
        }, $bodies));

        $size = pack('N', strlen($num.$mb));

        $buffer = 'MPUB '.$topic.PHP_EOL.$size.$num.$mb;

        $this->connection->write($buffer);
        $this->connection->read();
    }

    /**
     * @psalm-suppress PossiblyFalseOperand
     */
    public function dpub(string $topic, int $deferTime, string $body): void
    {
        $size = pack('N', strlen($body));

        $buffer = sprintf('DPUB %s %s', $topic, $deferTime).PHP_EOL.$size.$body;

        $this->connection->write($buffer);
        $this->connection->read();
    }
}
