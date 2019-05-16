<?php

declare(strict_types=1);

namespace App\Entity\Landlord\MC;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\MC\Equipment", inversedBy="lines")
     */
    public $equipment;

    /**
     * @var Work
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\MC\Work")
     */
    public $work;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Landlord\MC\Part", mappedBy="line")
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
