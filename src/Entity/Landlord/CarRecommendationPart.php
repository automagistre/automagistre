<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedBy;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Price;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class CarRecommendationPart
{
    use Identity;
    use Price;
    use CreatedAt;
    use CreatedBy;

    /**
     * @var CarRecommendation
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\CarRecommendation", inversedBy="parts")
     * @ORM\JoinColumn
     */
    private $recommendation;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\Part")
     * @ORM\JoinColumn
     */
    private $part;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function __construct(
        CarRecommendation $recommendation,
        Part $part,
        int $quantity,
        Money $price,
        User $selector
    ) {
        $this->recommendation = $recommendation;
        $this->part = $part;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->createdBy = $selector;
    }

    public function getRecommendation(): CarRecommendation
    {
        return $this->recommendation;
    }

    public function getPart(): Part
    {
        return $this->part;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
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
