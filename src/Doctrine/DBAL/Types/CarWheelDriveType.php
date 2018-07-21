<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

use App\Enum\CarWheelDrive;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarWheelDriveType extends EnumType
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'car_wheel_drive_enum';
    }

    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return CarWheelDrive::class;
    }
}
