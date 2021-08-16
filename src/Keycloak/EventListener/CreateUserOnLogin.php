<?php

declare(strict_types=1);

namespace App\Keycloak\EventListener;

use App\Keycloak\Event\UserLoggedIn;
use App\Tenant\State;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use function Sentry\captureMessage;
use function trim;

final class CreateUserOnLogin implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private State $state,
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

        $username = trim((string) $request->request->get('_username'));

        if ('' === $username) {
            captureMessage('Catch empty username on InteractiveLogin');

            return;
        }

        $this->messageBus->dispatch(
            new UserLoggedIn(
                $username,
                (string) $request->request->get('_password'),
                $this->state->get(),
            ),
        );
    }
}
