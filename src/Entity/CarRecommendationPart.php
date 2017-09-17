<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedAt;
use App\Entity\Traits\Identity;
use App\Entity\Traits\Price;
use App\Money\TotalPriceInterface;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class CarRecommendationPart implements TotalPriceInterface
{
    use Identity;
    use Price;
    use CreatedAt;

    /**
     * @var CarRecommendation
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CarRecommendation", inversedBy="parts")
     * @ORM\JoinColumn
     */
    private $recommendation;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Part")
     * @ORM\JoinColumn
     */
    private $part;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn
     */
    private $selector;

    public function __construct(
        CarRecommendation $recommendation,
        User $selector,
        Part $part,
        int $quantity,
        Money $price
    ) {
        $this->recommendation = $recommendation;
        $this->part = $part;
        $this->quantity = $quantity;
        $this->changePrice($price);
        $this->selector = $selector;
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

    public function setPrice(Money $price): void
    {
        $this->changePrice($price);
    }

    public function getSelector(): User
    {
        return $this->selector;
    }

    public function getTotalPrice(): Money
    {
        return $this->getPrice()->multiply($this->quantity / 100);
    }
}
