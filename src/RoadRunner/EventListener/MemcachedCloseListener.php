<?php

declare(strict_types=1);

namespace App\RoadRunner\EventListener;

use Memcached;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MemcachedCloseListener implements EventSubscriberInterface
{
    /**
     * @var Memcached
     */
    private $memcached;

    public function __construct(Memcached $memcached)
    {
        $this->memcached = $memcached;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -9999],
        ];
    }

    public function onKernelResponse(FilterResponseEvent $event): void
    {
        $this->memcached->quit();
    }
}
