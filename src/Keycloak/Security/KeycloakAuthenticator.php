<?php

declare(strict_types=1);

namespace App\Keycloak\Security;

use App\Keycloak\Constants;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use function is_string;

final class KeycloakAuthenticator extends AbstractAuthenticator
{
    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        return Constants::CALLBACK_ROUTE === $request->attributes->get('_route')
            && $request->query->has('code')
            && $request->query->has('state')
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(Request $request): PassportInterface
    {
        $session = $request->getSession();

        if ($request->query->get('state') !== $session->remove(Constants::OAUTH_2_STATE)) {
            throw new BadCredentialsException('Invalid state.');
        }

        $code = (string) $request->query->get('code');

        return new SelfValidatingPassport(new UserBadge($code));
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $redirectTo = $request->getSession()->remove(Constants::REDIRECT_TO);

        if (is_string($redirectTo)) {
            return new RedirectResponse($redirectTo);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        throw $exception;
    }
}
