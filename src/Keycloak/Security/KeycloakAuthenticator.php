<?php

declare(strict_types=1);

namespace App\Keycloak\Security;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token\Plain;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use function assert;
use function sprintf;

final class KeycloakAuthenticator extends AbstractAuthenticator
{
    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        return $request->headers->has('X-Forwarded-Access-Token');
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(Request $request): PassportInterface
    {
        /** @var string $jwt */
        $jwt = $request->headers->get('X-Forwarded-Access-Token');

        $token = Configuration::forUnsecuredSigner()->parser()->parse($jwt);
        assert($token instanceof Plain);
        $array = $token->claims()->get('automagistre') ?? throw new CustomUserMessageAuthenticationException('Token not contain `automagistre` claim.');

        $userId = $array['user_id'] ?? throw new CustomUserMessageAuthenticationException(sprintf('User "%s" haven\'t user_id attribute.', $token->claims()->get('email') ?? ''));

        return new SelfValidatingPassport(new UserBadge($userId));
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($exception instanceof BadCredentialsException && $exception->getPrevious() instanceof UserNotFoundException) {
            return new JsonResponse([
                'message' => 'You don\'t have access to this tenant.',
            ], Response::HTTP_FORBIDDEN);
        }

        throw $exception;
    }
}
