<?php

declare(strict_types=1);

namespace App\Entity\Landlord\MC;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Part
{
    use Identity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\MC\Line", inversedBy="parts")
     */
    public ?Line $line = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\Part")
     */
    public ?\App\Entity\Landlord\Part $part = null;

    /**
     * @ORM\Column(type="integer")
     */
    public ?int $quantity = null;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $recommended = false;
}
