<?php

declare(strict_types=1);

namespace App\Car\Entity;

use App\Customer\Entity\OperandId;
use App\Order\Entity\OrderItemService;
use App\Shared\Doctrine\ORM\Mapping\Traits\Price;
use App\Shared\Money\PriceInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="car_recommendation")
 */
class Recommendation implements PriceInterface
{
    use Price;

    /**
     * @ORM\Id()
     * @ORM\Column(type="recommendation_id")
     */
    public RecommendationId $id;

    /**
     * @var Car
     *
     * @ORM\ManyToOne(targetEntity=Car::class, inversedBy="recommendations")
     * @ORM\JoinColumn
     */
    public $car;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column
     */
    public $service;

    /**
     * @ORM\Column(type="operand_id")
     */
    public OperandId $workerId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?DateTime $expiredAt = null;

    /**
     * @ORM\Column(type="uuid", nullable=true)
     */
    private ?UuidInterface $realization = null;

    /**
     * @var Collection<int, RecommendationPart>
     *
     * @ORM\OneToMany(
     *     targetEntity=RecommendationPart::class,
     *     mappedBy="recommendation",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     * @ORM\OrderBy({"id": "ASC"})
     */
    private $parts;

    public function __construct(RecommendationId $id, Car $car, string $service, Money $price, OperandId $workerId)
    {
        $this->id = $id;
        $this->parts = new ArrayCollection();

        $this->car = $car;
        $this->service = $service;
        $this->price = $price;
        $this->workerId = $workerId;
    }

    public function __toString(): string
    {
        return $this->service;
    }

    public function toId(): RecommendationId
    {
        return $this->id;
    }

    public function setPrice(Money $price): void
    {
        $this->price = $price;
    }

    public function getTotalPrice(): Money
    {
        return $this->getTotalPartPrice()->add($this->getPrice());
    }

    public function getTotalPartPrice(): Money
    {
        $price = new Money(0, new Currency('RUB'));

        foreach ($this->parts as $part) {
            $price = $price->add($part->getTotalPrice());
        }

        return $price;
    }

    /**
     * @return RecommendationPart[]
     */
    public function getParts(): array
    {
        return $this->parts->toArray();
    }

    public function addPart(RecommendationPart $part): void
    {
        $this->parts[] = $part;
    }

    public function getRealization(): ?UuidInterface
    {
        return $this->realization;
    }

    public function realize(OrderItemService $orderItemService): void
    {
        $this->realization = $orderItemService->toId();
        $this->expiredAt = new DateTime();
    }
}
