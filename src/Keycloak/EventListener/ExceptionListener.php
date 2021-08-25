<?php

declare(strict_types=1);

namespace App\Keycloak\EventListener;

use App\Keycloak\Constants;
use Stevenmaguire\OAuth2\Client\Provider\Keycloak;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

final class ExceptionListener implements EventSubscriberInterface
{
    public function __construct(
        private Keycloak $provider,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable()->getPrevious();

        if (!$throwable instanceof InsufficientAuthenticationException) {
            return;
        }

        $request = $event->getRequest();

        $redirectUrl = $this->provider->getAuthorizationUrl([
            'redirect_uri' => $this->urlGenerator->generate(Constants::REDIRECT_ROUTE, referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
            'scope' => 'email',
        ]);

        $session = $request->getSession();
        $session->set(Constants::REDIRECT_TO, $request->getUri());
        $session->set(Constants::OAUTH_2_STATE, $this->provider->getState());

        $event->setResponse(new RedirectResponse($redirectUrl));
    }
}
