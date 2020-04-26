<?php

declare(strict_types=1);

namespace App\Entity\Landlord\MC;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Part\Domain\Part as BasePart;
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
     * @ORM\ManyToOne(targetEntity=BasePart::class)
     */
    public BasePart $part;

    /**
     * @ORM\Column(type="integer")
     */
    public int $quantity;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $recommended;

    public function __construct(Line $line, BasePart $part, int $quantity, bool $recommended)
    {
        $this->line = $line;
        $this->part = $part;
        $this->quantity = $quantity;
        $this->recommended = $recommended;
    }
}
