<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Doctrine\ORM\Type\Identifier;

/**
 * @psalm-immutable
 */
final class OrderId extends Identifier
{
}
