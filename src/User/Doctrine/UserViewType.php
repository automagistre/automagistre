<?php

declare(strict_types=1);

namespace App\User\Doctrine;

use App\User\Entity\UserId;
use App\User\Entity\UserView;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use function explode;

final class UserViewType extends Type
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        [$uuid, $username, $lastName, $firstName] = explode(',', $value);

        return new UserView(
            UserId::from($uuid),
            $username,
            $lastName,
            $firstName,
        );
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    public function getName(): string
    {
        return 'user_view';
    }
}
