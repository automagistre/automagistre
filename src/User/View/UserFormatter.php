<?php

declare(strict_types=1);

namespace App\User\View;

use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use App\User\Entity\User;
use App\User\Entity\UserId;
use Premier\Identifier\Identifier;

final class UserFormatter implements IdentifierFormatterInterface
{
    public function __construct(private Registry $registry)
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return UserId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $user = $this->registry->get(User::class, $identifier);

        return $user->__toString();
    }
}
