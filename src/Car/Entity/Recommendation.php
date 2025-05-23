<?php

declare(strict_types=1);

namespace App\Car\Entity;

use App\Keycloak\Entity\UserId;
use App\Customer\Entity\OperandId;
use App\Money\PriceInterface;
use App\Order\Entity\OrderItemService;
use App\Tenant\Entity\TenantGroupEntity;
use DateTime;
use DateTimeImmutable;
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
class Recommendation extends TenantGroupEntity implements PriceInterface
{
    /**
     * @ORM\Id
     * @ORM\Column
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
     * @ORM\Embedded(class=Money::class)
     */
    private Money $price;

    /**
     * @ORM\Column
     */
    public OperandId $workerId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?DateTime $expiredAt = null;

    /**
     * @ORM\Column(nullable=true)
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

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

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

    public function getPrice(): Money
    {
        return $this->price;
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
