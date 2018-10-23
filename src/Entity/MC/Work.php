<?php

declare(strict_types=1);

namespace App\Entity\MC;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Part;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Work
{
    use Identity;

    /**
     * @var Equipment
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\MC\Equipment")
     */
    public $equipment;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Part")
     */
    public $part;

    /**
     * @var float
     *
     * @ORM\Column(type="float", precision=2)
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
