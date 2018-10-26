<?php

declare(strict_types=1);

namespace App\Entity\MC;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\MC\Line", inversedBy="parts")
     */
    public $line;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Part")
     */
    public $part;

    /**
     * @var float
     *
     * @ORM\Column(type="float", length=2)
     */
    public $quantity;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    public $recommended;
}
