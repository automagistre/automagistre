<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Tenant\Entity\TenantEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="part_discount", indexes={@ORM\Index(columns={"part_id"})})
 */
class Discount extends TenantEntity
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
     * @ORM\Embedded(class=Money::class)
     */
    private Money $discount;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $since;

    public function __construct(PartId $partId, Money $discount, DateTimeImmutable $since = null)
    {
        $this->id = Uuid::uuid6();
        $this->partId = $partId;
        $this->discount = $discount;
        $this->since = $since ?? new DateTimeImmutable();
    }
}
