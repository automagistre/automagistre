<?php

declare(strict_types=1);

namespace App\MC\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class McLine
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

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

    /**
     * @ORM\Column(type="integer")
     */
    public int $position;

    public function __construct(
        UuidInterface $id,
        McEquipment $equipment,
        McWork $work,
        int $period,
        bool $recommended,
        int $position = 0
    ) {
        $this->id = $id;
        $this->equipment = $equipment;
        $this->work = $work;
        $this->parts = new ArrayCollection();
        $this->period = $period;
        $this->recommended = $recommended;
        $this->position = $position;
    }

    public function toId(): UuidInterface
    {
        return $this->id;
    }
}
