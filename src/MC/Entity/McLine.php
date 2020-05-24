<?php

declare(strict_types=1);

namespace App\MC\Entity;

use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class McLine
{
    use Identity;

    /**
     * @ORM\ManyToOne(targetEntity=McEquipment::class, inversedBy="lines")
     */
    public McEquipment $equipment;

    /**
     * @ORM\ManyToOne(targetEntity=McWork::class)
     */
    public McWork $work;

    /**
     * @var Collection<int, McPart>
     *
     * @ORM\OneToMany(targetEntity=McPart::class, mappedBy="line")
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

    public function __construct(McEquipment $equipment, McWork $work, int $period, bool $recommended)
    {
        $this->equipment = $equipment;
        $this->work = $work;
        $this->parts = new ArrayCollection();
        $this->period = $period;
        $this->recommended = $recommended;
    }
}
