<?php

declare(strict_types=1);

namespace App\CreatedBy\Doctrine;

use App\CreatedBy\Entity\CreatedByView;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use function explode;

final class CreatedByViewType extends Type
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        [$user, $date] = explode(';', $value);

        return new CreatedByView(
            Type::getType('user_view')->convertToPHPValue($user, $platform),
            Type::getType('datetime_immutable')->convertToPHPValue($date, $platform),
        );
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    public function getName(): string
    {
        return 'created_by_view';
    }
}
