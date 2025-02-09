<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @psalm-immutable
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="part_required_availability")
 */
class RequiredAvailability extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    private UuidInterface $id;

    /**
     * @ORM\Column
     */
    private PartId $partId;

    /**
     * @ORM\Column(type="integer")
     */
    private int $orderFromQuantity;

    /**
     * @ORM\Column(type="integer")
     */
    private int $orderUpToQuantity;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(PartId $partId, int $orderFromQuantity, int $orderUpToQuantity)
    {
        $this->id = Uuid::uuid6();
        $this->partId = $partId;
        $this->orderFromQuantity = $orderFromQuantity;
        $this->orderUpToQuantity = $orderUpToQuantity;
    }
}
