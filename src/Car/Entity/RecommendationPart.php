<?php

declare(strict_types=1);

namespace App\Car\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedBy;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Price;
use App\Entity\Landlord\Part;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="car_recommendation_part")
 */
class RecommendationPart
{
    use Identity;
    use Price;
    use CreatedAt;
    use CreatedBy;

    /**
     * @ORM\Column(type="uuid", unique=true)
     */
    public UuidInterface $uuid;

    /**
     * @psalm-readonly
     *
     * @ORM\ManyToOne(targetEntity="App\Car\Entity\Recommendation", inversedBy="parts")
     * @ORM\JoinColumn
     */
    public Recommendation $recommendation;

    /**
     * @psalm-readonly
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\Part")
     * @ORM\JoinColumn
     */
    public ?Part $part = null;

    /**
     * @ORM\Column(type="integer")
     */
    public int $quantity = 0;

    public function __construct(Recommendation $recommendation, Part $part, int $quantity, Money $price, User $selector)
    {
        $this->uuid = Uuid::uuid4();
        $this->recommendation = $recommendation;
        $this->part = $part;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->createdBy = $selector;
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
