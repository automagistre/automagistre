<?php

declare(strict_types=1);

namespace App\Keycloak\EventListener;

use Keycloak\Admin\KeycloakClient;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use function sprintf;
use function urlencode;

final class LogoutListener implements EventSubscriberInterface
{
    public function __construct(
        private KeycloakClient $keycloak,
        private UrlGeneratorInterface $urlGenerator,
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
        $ssoLogoutUrl = sprintf(
            '%s/auth/realms/%s/protocol/openid-connect/logout?redirect_uri=%s',
            $this->keycloak->getConfig('baseUri'),
            $this->keycloak->getRealmName() ?? throw new LogicException('realm required.'),
            $this->urlGenerator->generate('easyadmin', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
        );

        $event->setResponse(new RedirectResponse('/oauth2/sign_out?rd='.urlencode($ssoLogoutUrl)));
    }
}
