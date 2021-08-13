<?php

declare(strict_types=1);

namespace App\Keycloak\EventListener;

use App\Keycloak\Event\UserLoggedIn;
use App\Tenant\Tenant;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class CreateUserOnLogin implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private Tenant $tenant,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            InteractiveLoginEvent::class => 'onLogin',
        ];
    }

    public function onLogin(InteractiveLoginEvent $event): void
    {
        $request = $event->getRequest();

        $this->messageBus->dispatch(
            new UserLoggedIn(
                (string) $request->request->get('_username'),
                (string) $request->request->get('_password'),
                $this->tenant,
            ),
        );
    }
}
