<?php

declare(strict_types=1);

namespace App\Appeal\Doctrine\Type;

use App\Appeal\Entity\TireWorkCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class TireFittingWorksType extends JsonType
{
    /**
     * {@inheritDoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): TireWorkCollection
    {
        /** @var array $value */
        $value = parent::convertToPHPValue($value, $platform);

        return new TireWorkCollection($value);
    }

    /**
     * {@inheritDoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'appeal_tire_fitting_work';
    }
}
