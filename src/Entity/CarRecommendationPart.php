<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\Price;
use App\Money\TotalPriceInterface;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class CarRecommendationPart implements TotalPriceInterface
{
    use Price;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

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
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): \DateTime
    {
        return clone $this->createdAt;
    }
}
