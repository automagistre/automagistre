<?php

declare(strict_types=1);

namespace App\Keycloak\Security;

use App\Keycloak\Constants;
use Stevenmaguire\OAuth2\Client\Provider\Keycloak;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

final class KeycloakEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(
        private Keycloak $provider,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $redirectUrl = $this->provider->getAuthorizationUrl([
            'redirect_uri' => $this->urlGenerator->generate(Constants::CALLBACK_ROUTE, referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
            'scope' => 'email',
        ]);

        $session = $request->getSession();
        $session->set(Constants::REDIRECT_TO, $request->getUri());
        $session->set(Constants::OAUTH_2_STATE, $this->provider->getState());

        return new RedirectResponse($redirectUrl);
    }
}
