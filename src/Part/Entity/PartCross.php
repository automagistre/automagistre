<?php

declare(strict_types=1);

namespace App\Part\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class PartCross
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @var Collection<int, Part>
     *
     * @ORM\ManyToMany(targetEntity=Part::class)
     * @ORM\JoinTable(inverseJoinColumns={@ORM\JoinColumn(unique=true)})
     */
    private $parts;

    public function __construct(Part $left, Part $right)
    {
        $this->id = Uuid::uuid6();
        $this->parts = new ArrayCollection();
        $this->addPart($left, $right);
    }

    public function toId(): UuidInterface
    {
        return $this->id;
    }

    public function isEmpty(): bool
    {
        return $this->parts->isEmpty();
    }

    public function addPart(Part ...$parts): void
    {
        foreach ($parts as $part) {
            if ($this->parts->contains($part)) {
                continue;
            }

            $this->parts[] = $part;
        }
    }

    public function removePart(Part ...$parts): void
    {
        foreach ($parts as $part) {
            $this->parts->removeElement($part);
        }
    }

    public function getParts(): array
    {
        return $this->parts->getValues();
    }
}
