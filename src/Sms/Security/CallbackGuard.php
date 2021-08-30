<?php

declare(strict_types=1);

namespace App\Sms\Security;

use App\Sms\Constants;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class CallbackGuard extends AbstractAuthenticator
{
    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        return 'callback.automagistre.ru' === $request->getHost()
            && 'smsaero' === $request->attributes->get('provider');
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(Request $request): PassportInterface
    {
        return new SelfValidatingPassport(new UserBadge(Constants::SMSAERO_USER_ID));
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
        throw $exception;
    }
}
