<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

use App\Enum\CarTransmission;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarTransmissionType extends EnumType
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'car_transmission_enum';
    }

    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return CarTransmission::class;
    }
}
