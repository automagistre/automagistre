<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedBy;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Price;
use App\Entity\Embeddable\OrderItemServiceRelation;
use App\Entity\Tenant\OrderItemService;
use App\Enum\Tenant;
use App\Money\PriceInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class CarRecommendation implements PriceInterface
{
    use Identity;
    use Price;
    use CreatedAt;
    use CreatedBy;

    /**
     * @var Car
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\Car", inversedBy="recommendations")
     * @ORM\JoinColumn
     */
    private $car;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column
     */
    private $service;

    /**
     * @var CarRecommendationPart[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Landlord\CarRecommendationPart",
     *     mappedBy="recommendation",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     */
    private $parts;

    /**
     * @var OrderItemServiceRelation
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\OrderItemServiceRelation")
     */
    private $realization;

    /**
     * @var Operand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\Operand")
     * @ORM\JoinColumn(nullable=false)
     */
    private $worker;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiredAt;

    public function __construct(Car $car, string $service, Money $price, Operand $worker, User $createdBy)
    {
        $this->parts = new ArrayCollection();
        $this->realization = new OrderItemServiceRelation();

        $this->car = $car;
        $this->service = $service;
        $this->price = $price;
        $this->worker = $worker;
        $this->setCreatedBy($createdBy);
    }

    public function __toString(): string
    {
        return $this->service;
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

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(?string $service): void
    {
        $this->service = $service;
    }

    public function setPrice(Money $price): void
    {
        $this->price = $price;
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

    public function getRealization(): ?OrderItemService
    {
        return $this->realization->entityOrNull();
    }

    public function getWorker(): Operand
    {
        return $this->worker;
    }

    public function setWorker(Operand $worker): void
    {
        $this->worker = $worker;
    }

    public function getExpiredAt(): ?DateTime
    {
        return $this->expiredAt;
    }

    public function realize(OrderItemService $orderItemService, Tenant $tenant): void
    {
        $this->realization = new OrderItemServiceRelation($orderItemService, $tenant);
        $this->expiredAt = new DateTime();
    }
}
