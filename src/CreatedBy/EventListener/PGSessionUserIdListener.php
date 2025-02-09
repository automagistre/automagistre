<?php

declare(strict_types=1);

namespace App\CreatedBy\EventListener;

use App\Costil;
use App\Doctrine\Registry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

final class PGSessionUserIdListener implements EventSubscriberInterface
{
    public function __construct(
        private Registry $registry,
        private Security $security,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        $userId = match (true) {
            null === $user && 'cli' === PHP_SAPI => Costil::SERVICE_USER,
            null !== $user => $user->getUserIdentifier(),
            default => Costil::ANONYMOUS,
        };

        $this->registry->connection()->executeQuery('SET app.user_id = \''.$userId.'\'');
    }
}
