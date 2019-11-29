<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedByRelation as CreatedBy;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Embeddable\OperandRelation;
use App\Entity\Embeddable\UserRelation;
use App\Entity\Landlord\Operand;
use App\Entity\Landlord\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use function sprintf;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 *
 * @ORM\Table(
 *     indexes={@ORM\Index(name="SEARCH_IDX", columns={"accrued_at"})}
 * )
 */
class Income
{
    use Identity;
    use CreatedAt;
    use CreatedBy;

    /**
     * @var OperandRelation
     *
     * @Assert\Valid
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\OperandRelation")
     */
    private $supplier;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    private $document;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Tenant\IncomePart", mappedBy="income",
     * orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $incomeParts;

    /**
     * @var DateTimeImmutable|null
     *
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private $accruedAt;

    /**
     * @var UserRelation
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\UserRelation")
     */
    private $accruedBy;

    /**
     * @var Money|null
     *
     * @ORM\Embedded(class="Money\Money")
     */
    private $accruedAmount;

    public function __construct()
    {
        $this->supplier = new OperandRelation();
        $this->accruedBy = new UserRelation();
        $this->incomeParts = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf(
            '№%s %s от %s',
            $this->getId(),
            (string) $this->getSupplier(),
            $this->getCreatedAt()->format('d.m.Y')
        );
    }

    public function isEditable(): bool
    {
        return null === $this->accruedAt;
    }

    public function getSupplier(): ?Operand
    {
        return $this->supplier->entityOrNull();
    }

    public function setSupplier(?Operand $supplier): void
    {
        $this->supplier = new OperandRelation($supplier);
    }

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(?string $document): void
    {
        $this->document = $document;
    }

    /**
     * @return IncomePart[]
     */
    public function getIncomeParts(): array
    {
        return $this->incomeParts->toArray();
    }

    public function addIncomePart(IncomePart $incomePart): void
    {
        $incomePart->setIncome($this);
        $this->incomeParts[] = $incomePart;
    }

    public function itemsCount(): int
    {
        return $this->incomeParts->count();
    }

    public function accrue(User $user): void
    {
        $this->accruedBy = new UserRelation($user);
        $this->accruedAt = new DateTimeImmutable();
        $this->accruedAmount = $this->getTotalPrice();
    }

    public function getAccruedAt(): ?DateTimeImmutable
    {
        return $this->accruedAt;
    }

    public function getAccruedBy(): ?User
    {
        return $this->accruedBy->entityOrNull();
    }

    public function getAccruedAmount(): ?Money
    {
        return $this->accruedAmount;
    }

    public function getTotalPrice(): Money
    {
        $money = new Money('0', new Currency('RUB'));
        foreach ($this->getIncomeParts() as $incomePart) {
            $money = $money->add($incomePart->getTotalPrice());
        }

        return $money;
    }
}
