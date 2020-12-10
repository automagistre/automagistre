<?php

declare(strict_types=1);

namespace App\Nsq;

final class Publisher
{
    private Config $config;

    private ?Connection $connection = null;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function pub(string $topic, string $message): void
    {
        $this->getConnection()->pub($topic, $message);
    }

    public function mpub(string $topic, array $bodies): void
    {
        $this->getConnection()->mpub($topic, $bodies);
    }

    public function dpub(string $topic, int $deferTime, string $body): void
    {
        $this->getConnection()->dpub($topic, $deferTime, $body);
    }

    private function getConnection(): Connection
    {
        return $this->connection ??= Connection::connect($this->config);
    }
}
