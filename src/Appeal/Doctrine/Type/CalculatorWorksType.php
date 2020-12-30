<?php

declare(strict_types=1);

namespace App\Appeal\Doctrine\Type;

use App\Appeal\Entity\CalculatorWorkCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class CalculatorWorksType extends JsonType
{
    /**
     * {@inheritDoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): CalculatorWorkCollection
    {
        /** @var array $value */
        $value = parent::convertToPHPValue($value, $platform);

        return new CalculatorWorkCollection($value);
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
        return 'appeal_calculator_work';
    }
}
