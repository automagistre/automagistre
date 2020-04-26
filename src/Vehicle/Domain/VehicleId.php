<?php

declare(strict_types=1);

namespace App\Vehicle\Domain;

use App\Doctrine\ORM\Type\Identifier;

/**
 * @psalm-immutable
 */
final class VehicleId extends Identifier
{
}
