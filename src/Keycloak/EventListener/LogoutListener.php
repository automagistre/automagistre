<?php

declare(strict_types=1);

namespace App\Keycloak\EventListener;

use Stevenmaguire\OAuth2\Client\Provider\Keycloak;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogoutListener implements EventSubscriberInterface
{
    public function __construct(
        private Keycloak $keycloak,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onLogout(LogoutEvent $event): void
    {
        $request = $event->getRequest();

        $ssoLogoutUrl = $this->keycloak->getLogoutUrl([
            'redirect_uri' => $request->headers->get('referer') ?? $request->getUriForPath('/'),
        ]);

        $event->setResponse(new RedirectResponse($ssoLogoutUrl));
    }
}
