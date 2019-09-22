<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Landlord\User;
use App\State;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TenantSecurityListener implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var State
     */
    private $state;

    public function __construct(TokenStorageInterface $tokenStorage, State $state)
    {
        $this->tokenStorage = $tokenStorage;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(): void
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return;
        }

        $current = $this->state->tenant();
        foreach ($user->getTenants() as $tenant) {
            if ($current->eq($tenant)) {
                return;
            }
        }

        throw new AccessDeniedHttpException(
            \sprintf('You are not permitted to access "%s" tenant', $current->getName())
        );
    }
}
