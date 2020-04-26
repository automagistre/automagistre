<?php

declare(strict_types=1);

namespace App\Car\Entity;

use App\Customer\Domain\Operand;
use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedBy;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Price;
use App\Entity\Embeddable\OrderItemServiceRelation;
use App\Entity\Tenant\OrderItemService;
use App\Money\PriceInterface;
use App\Tenant\Tenant;
use App\User\Entity\User;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="car_recommendation")
 */
class Recommendation implements PriceInterface
{
    use Identity;
    use Price;
    use CreatedAt;
    use CreatedBy;

    /**
     * @ORM\Column(type="recommendation_id", unique=true)
     */
    public RecommendationId $uuid;

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
     * @var Operand
     *
     * @psalm-readonly
     *
     * @ORM\ManyToOne(targetEntity=Operand::class)
     * @ORM\JoinColumn(nullable=false)
     */
    public $worker;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?DateTime $expiredAt = null;

    /**
     * @ORM\Embedded(class=OrderItemServiceRelation::class)
     */
    private ?OrderItemServiceRelation $realization = null;

    /**
     * @var Collection<int, RecommendationPart>
     *
     * @ORM\OneToMany(
     *     targetEntity=RecommendationPart::class,
     *     mappedBy="recommendation",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     * @ORM\OrderBy({"createdAt": "ASC"})
     */
    private $parts;

    public function __construct(Car $car, string $service, Money $price, Operand $worker, User $user)
    {
        $this->uuid = RecommendationId::generate();
        $this->parts = new ArrayCollection();
        $this->realization = new OrderItemServiceRelation();

        $this->car = $car;
        $this->service = $service;
        $this->price = $price;
        $this->worker = $worker;
        $this->createdBy = $user;
    }

    public function __toString(): string
    {
        return $this->service;
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

    public function getRealization(): ?OrderItemService
    {
        return $this->realization->entityOrNull();
    }

    public function realize(OrderItemService $orderItemService, Tenant $tenant): void
    {
        $this->realization = new OrderItemServiceRelation($orderItemService, $tenant);
        $this->expiredAt = new DateTime();
    }
}
