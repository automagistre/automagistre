<?php

declare(strict_types=1);

namespace App\Car\Entity;

use App\Part\Entity\PartId;
use App\Tenant\Entity\TenantGroupEntity;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 * @ORM\Table(name="car_recommendation_part")
 */
class RecommendationPart extends TenantGroupEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public RecommendationPartId $id;

    /**
     * @psalm-readonly
     *
     * @ORM\ManyToOne(targetEntity=Recommendation::class, inversedBy="parts")
     * @ORM\JoinColumn(nullable=false)
     */
    public Recommendation $recommendation;

    /**
     * @ORM\Column
     */
    public PartId $partId;

    /**
     * @ORM\Column(type="integer")
     */
    public int $quantity = 0;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    public Money $price;

    public function __construct(
        RecommendationPartId $id,
        Recommendation $recommendation,
        PartId $partId,
        int $quantity,
        Money $price,
    ) {
        $this->id = $id;
        $this->recommendation = $recommendation;
        $this->partId = $partId;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public function toId(): RecommendationPartId
    {
        return $this->id;
    }

    public function getTotalPrice(): Money
    {
        return $this->price->multiply((string) ($this->quantity / 100));
    }
}
