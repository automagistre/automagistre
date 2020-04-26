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
     * @ORM\ManyToOne(targetEntity=Line::class, inversedBy="parts")
     */
    public ?Line $line;

    /**
     * @ORM\ManyToOne(targetEntity=App\Entity\Landlord\Part::class)
     */
    public \App\Entity\Landlord\Part $part;

    /**
     * @ORM\Column(type="integer")
     */
    public int $quantity;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $recommended;

    public function __construct(Line $line, \App\Entity\Landlord\Part $part, int $quantity, bool $recommended)
    {
        $this->line = $line;
        $this->part = $part;
        $this->quantity = $quantity;
        $this->recommended = $recommended;
    }
}
