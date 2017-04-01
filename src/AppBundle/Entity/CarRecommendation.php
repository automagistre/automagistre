<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CarRecommendation
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
     * @var Car
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Car", inversedBy="recommendations")
     * @ORM\JoinColumn()
     */
    private $car;

    /**
     * @var Service
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Service")
     */
    private $service;

    /**
     * @var CarRecommendationPart[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CarRecommendationPart", mappedBy="recommendation")
     */
    private $parts;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $cost;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Order")
     */
    private $realization;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiredAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct(Car $car)
    {
        $this->car = $car;
        $this->parts = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(Service $service): void
    {
        $this->service = $service;
    }

    public function getParts(): array
    {
        return $this->parts->toArray();
    }

    public function addPart(CarRecommendationPart $part): void
    {
        $this->parts[] = $part;
    }

    public function getRealization(): Order
    {
        return $this->realization;
    }

    public function setRealization(Order $realization): void
    {
        $this->realization = $realization;
    }

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function setCost(string $cost)
    {
        $this->cost = $cost;
    }

    public function getPartsCost(): int
    {
        return array_sum($this->parts->map(function (CarRecommendationPart $part) {
            return $part->getTotalCost();
        })->toArray());
    }

    public function getExpiredAt(): \DateTime
    {
        return $this->expiredAt;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function __toString(): string
    {
        return $this->service->getName();
    }
}
