<?php

namespace AppBundle\Entity;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CarRecommendation", inversedBy="parts")
     * @ORM\JoinColumn()
     */
    private $recommendation;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Part")
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

    public function __construct(
        CarRecommendation $recommendation,
        Part $part = null,
        int $quantity = null,
        int $price = null
    ) {
        $this->recommendation = $recommendation;
        $this->part = $part;
        $this->quantity = $quantity;
        $this->cost = $price;
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
}
