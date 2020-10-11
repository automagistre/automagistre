<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Part\Entity\PartId;
use App\Storage\Enum\Source;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

final class MotionalPart
{
    private PartId $partId;

    private EntityManagerInterface $em;

    public function __construct(PartId $partId, EntityManagerInterface $em)
    {
        $this->partId = $partId;
        $this->em = $em;
    }

    public function move(int $quantity, Source $source, UuidInterface $sourceId, string $description = null): void
    {
        $this->em->persist(
            new Motion(
                $this->partId,
                $quantity,
                $source,
                $sourceId,
                $description,
            )
        );
    }
}
