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
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\MC\Equipment", inversedBy="lines")
     */
    public ?Equipment $equipment = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\MC\Work")
     */
    public ?Work $work = null;

    /**
     * @var Collection<int, Part>|iterable
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Landlord\MC\Part", mappedBy="line")
     */
    public iterable $parts = [];

    /**
     * @ORM\Column(type="integer")
     */
    public ?int $period = null;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $recommended = false;

    public function __construct()
    {
        $this->parts = new ArrayCollection();
    }
}
