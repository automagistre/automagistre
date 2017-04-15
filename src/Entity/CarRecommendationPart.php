<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CarRecommendationPart
{
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
     * @ORM\JoinColumn()
     */
    private $recommendation;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Part")
     * @ORM\JoinColumn()
     */
    private $part;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $cost;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn()
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
        Part $part = null,
        int $quantity = null,
        int $price = null
    ) {
        $this->recommendation = $recommendation;
        $this->part = $part;
        $this->quantity = $quantity;
        $this->cost = $price;
        $this->selector = $selector;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecommendation(): ?CarRecommendation
    {
        return $this->recommendation;
    }

    public function getPart(): ?Part
    {
        return $this->part;
    }

    public function setPart(Part $part): void
    {
        if ($this->part) {
            throw new \DomainException('Changing part is restricted');
        }

        $this->part = $part;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(int $cost): void
    {
        $this->cost = $cost;
    }

    public function getTotalCost(): int
    {
        return $this->getCost() * $this->getQuantity();
    }

    public function getSelector(): ?User
    {
        return $this->selector;
    }

    public function setSelector(User $selector): void
    {
        $this->selector = $selector;
    }

    public function getCreatedAt(): \DateTime
    {
        return clone $this->createdAt;
    }
}
