<?php

declare(strict_types=1);

namespace App\JSONRPC\Test;

use function assert;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class JsonRPCTestCase extends WebTestCase
{
    /**
     * @param mixed[] $options
     * @param mixed[] $server
     *
     * {@inheritdoc}
     */
    protected static function createClient(array $options = [], array $server = []): JsonRPCClient
    {
        $client = parent::createClient($options, $server);

        assert($client instanceof JsonRPCClient);

        return $client;
    }
}
