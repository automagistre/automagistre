<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="part_cross_part")
 */
class PartCross extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", name="part_cross_id")
     */
    public UuidInterface $id;

    /**
     * @ORM\Id
     * @ORM\Column(type="part_id")
     */
    public PartId $partId;

    public function __construct(UuidInterface $id, PartId $partId)
    {
        $this->id = $id;
        $this->partId = $partId;
    }
}
