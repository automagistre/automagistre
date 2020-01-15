<?php

namespace App\Tenant;

use App\User\Entity\User;
use function assert;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TenantVoter extends Voter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        return 'ACCESS' === $attribute && $subject instanceof Tenant;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        assert($subject instanceof Tenant);
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        foreach ($user->getTenants() as $tenant) {
            if ($subject->eq($tenant)) {
                return true;
            }
        }

        return false;
    }
}
