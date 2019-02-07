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
     * @var Line
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\MC\Line", inversedBy="parts")
     */
    public $line;

    /**
     * @var \App\Entity\Landlord\Part
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\Part")
     */
    public $part;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    public $quantity;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    public $recommended;
}
