<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Customer\Entity\OperandId;
use App\Part\Enum\SupplySource;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="part_supply")
 */
class Supply extends TenantEntity
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
     * @ORM\Column
     */
    private OperandId $supplierId;

    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    /**
     * @ORM\Column(type="part_supply_source_enum")
     */
    private SupplySource $source;

    /**
     * @ORM\Column
     */
    private UuidInterface $sourceId;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(
        PartId $partId,
        OperandId $supplierId,
        int $quantity,
        SupplySource $source,
        UuidInterface $sourceId,
    ) {
        $this->id = Uuid::uuid6();
        $this->partId = $partId;
        $this->supplierId = $supplierId;
        $this->quantity = $quantity;
        $this->source = $source;
        $this->sourceId = $sourceId;
    }
}
