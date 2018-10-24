<?php

declare(strict_types=1);

namespace App\Entity\MC;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Line
{
    use Identity;

    /**
     * @var Equipment
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\MC\Equipment", inversedBy="lines")
     */
    public $equipment;

    /**
     * @var Work
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\MC\Work")
     */
    public $work;

    /**
     * @var Part[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\MC\Part", mappedBy="line")
     */
    public $parts;

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

    public function __construct()
    {
        $this->parts = new ArrayCollection();
    }
}
