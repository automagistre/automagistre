<?php

declare(strict_types=1);

namespace App\Part\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="part_discount", indexes={@ORM\Index(columns={"part_id"})})
 */
class Discount
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="part_id")
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
