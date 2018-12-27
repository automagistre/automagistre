<?php

declare(strict_types=1);

namespace App\EventListener;

use LongRunning\Core\Cleaner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CleanerListener implements EventSubscriberInterface
{
    /**
     * @var Cleaner
     */
    private $cleaner;

    public function __construct(Cleaner $cleaner)
    {
        $this->cleaner = $cleaner;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE => ['onKernelTerminate', -9999],
        ];
    }

    public function onKernelTerminate(): void
    {
        $this->cleaner->cleanUp();
    }
}
