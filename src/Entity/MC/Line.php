<?php

declare(strict_types=1);

namespace App\Entity\MC;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Part;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Line
{
    use Identity;

    /**
     * @var Work
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\MC\Work")
     */
    public $work;

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
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    public $period;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    public $recommended;
}
