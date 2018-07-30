<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Price;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class CarRecommendation
{
    use Identity;
    use Price;
    use CreatedAt;

    /**
     * @var Car
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Car", inversedBy="recommendations")
     * @ORM\JoinColumn
     */
    private $car;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $service;

    /**
     * @var CarRecommendationPart[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\CarRecommendationPart",
     *     mappedBy="recommendation",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     */
    private $parts;

    /**
     * @var Order|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Order")
     */
    private $realization;

    /**
     * @var Operand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand")
     * @ORM\JoinColumn(nullable=false)
     */
    private $worker;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiredAt;

    public function __construct(Car $car, string $service, Money $price, Operand $worker)
    {
        $this->parts = new ArrayCollection();

        $this->car = $car;
        $this->service = $service;
        $this->changePrice($price);
        $this->worker = $worker;
    }

    public function __toString(): string
    {
        return $this->service;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setPrice(Money $price): void
    {
        $this->changePrice($price);
    }

    /**
     * @return CarRecommendationPart[]
     */
    public function getParts(): array
    {
        return $this->parts->toArray();
    }

    public function addPart(CarRecommendationPart $part): void
    {
        $this->parts[] = $part;
    }

    public function getRealization(): ?Order
    {
        return $this->realization;
    }

    public function getWorker(): Operand
    {
        return $this->worker;
    }

    public function getExpiredAt(): ?DateTime
    {
        return $this->expiredAt;
    }

    public function realize(Order $order): void
    {
        $this->realization = $order;
        $this->expiredAt = new DateTime();
        $this->parts->clear();
    }
}
