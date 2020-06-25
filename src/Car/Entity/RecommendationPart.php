<?php

declare(strict_types=1);

namespace App\Car\Entity;

use App\Part\Entity\PartId;
use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use App\Shared\Doctrine\ORM\Mapping\Traits\Price;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 * @ORM\Table(name="car_recommendation_part")
 */
class RecommendationPart
{
    use Identity;
    use Price;

    /**
     * @ORM\Column(type="recommendation_part_id", unique=true)
     */
    public RecommendationPartId $uuid;

    /**
     * @psalm-readonly
     *
     * @ORM\ManyToOne(targetEntity=Recommendation::class, inversedBy="parts")
     * @ORM\JoinColumn
     */
    public Recommendation $recommendation;

    /**
     * @ORM\Column(type="part_id")
     */
    public PartId $partId;

    /**
     * @ORM\Column(type="integer")
     */
    public int $quantity = 0;

    public function __construct(Recommendation $recommendation, PartId $partId, int $quantity, Money $price)
    {
        $this->uuid = RecommendationPartId::generate();
        $this->recommendation = $recommendation;
        $this->partId = $partId;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public function toId(): RecommendationPartId
    {
        return $this->uuid;
    }

    public function setPrice(Money $price): void
    {
        $this->price = $price;
    }

    public function getTotalPrice(): Money
    {
        return $this->getPrice()->multiply($this->quantity / 100);
    }
}
