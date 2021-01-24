<?php

declare(strict_types=1);

namespace App\MessageBus;

use App\Nsq\Config;
use App\Tenant\Tenant;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use function strpos;

final class NsqTransportFactory implements TransportFactoryInterface
{
    private Tenant $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        return new NsqTransport(
            new Config(),
            $serializer,
            $this->tenant->toBusTopic(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $dsn, array $options): bool
    {
        return 0 === strpos($dsn, 'nsq://');
    }
}
