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
     * @ORM\ManyToOne(targetEntity=Equipment::class, inversedBy="lines")
     */
    public Equipment $equipment;

    /**
     * @ORM\ManyToOne(targetEntity=Work::class)
     */
    public Work $work;

    /**
     * @var Collection<int, Part>|iterable
     *
     * @ORM\OneToMany(targetEntity=Part::class, mappedBy="line")
     */
    public iterable $parts;

    /**
     * @ORM\Column(type="integer")
     */
    public int $period;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $recommended = false;

    public function __construct(Equipment $equipment, Work $work, int $period, bool $recommended)
    {
        $this->equipment = $equipment;
        $this->work = $work;
        $this->parts = new ArrayCollection();
        $this->period = $period;
        $this->recommended = $recommended;
    }
}
