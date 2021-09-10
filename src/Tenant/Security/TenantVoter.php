<?php

declare(strict_types=1);

namespace App\Tenant\Security;

use App\Doctrine\Registry;
use App\Keycloak\Entity\Permission;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

final class TenantVoter extends Voter
{
    public const TENANT_ACCESS = 'TENANT_ACCESS';

    public function __construct(private Registry $registry, private Security $security)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, $subject): bool
    {
        return self::TENANT_ACCESS === $attribute && $subject instanceof Request;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $user = $token->getUser();

        $username = match (true) {
            $user instanceof UserInterface => $user->getUserIdentifier(),
            default => $user,
        };

        return null !== $this->registry->findOneBy(Permission::class, ['userId' => $username]);
    }
}
