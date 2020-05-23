<?php

declare(strict_types=1);

namespace App\MC\Entity;

use App\Part\Domain\Part;
use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class McPart
{
    use Identity;

    /**
     * @ORM\ManyToOne(targetEntity=McLine::class, inversedBy="parts")
     */
    public ?McLine $line;

    /**
     * @ORM\ManyToOne(targetEntity=Part::class)
     */
    public Part $part;

    /**
     * @ORM\Column(type="integer")
     */
    public int $quantity;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $recommended;

    public function __construct(McLine $line, Part $part, int $quantity, bool $recommended)
    {
        $this->line = $line;
        $this->part = $part;
        $this->quantity = $quantity;
        $this->recommended = $recommended;
    }
}
