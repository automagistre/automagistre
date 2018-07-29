<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="integer")
 * @ORM\DiscriminatorMap({"1": "Person", "2": "Organization"})
 */
abstract class Operand
{
    use Identity;

    abstract public function __toString(): string;

    abstract public function getFullName(): string;

    abstract public function getTelephone(): ?string;
}
