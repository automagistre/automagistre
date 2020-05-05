<?php

declare(strict_types=1);

namespace App\Income\Entity;

use App\Customer\Domain\Operand;
use App\Doctrine\ORM\Mapping\Traits\CreatedByRelation as CreatedBy;
use App\Entity\Embeddable\OperandRelation;
use App\Entity\Embeddable\UserRelation;
use App\User\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 *
 * @ORM\Table(
 *     indexes={@ORM\Index(columns={"accrued_at"})}
 * )
 */
class Income
{
    use CreatedBy;

    /**
     * @ORM\Id()
     * @ORM\Column(type="income_id")
     */
    private IncomeId $id;

    /**
     * @var OperandRelation
     *
     * @Assert\Valid
     *
     * @ORM\Embedded(class=OperandRelation::class)
     */
    private $supplier;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    private $document;

    /**
     * @var Collection<int, IncomePart>
     *
     * @ORM\OneToMany(
     *     targetEntity=IncomePart::class,
     *     mappedBy="income",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     * )
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
     * @ORM\Embedded(class=UserRelation::class)
     */
    private $accruedBy;

    /**
     * @var Money|null
     *
     * @ORM\Embedded(class=Money::class)
     */
    private $accruedAmount;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    public function __construct(IncomeId $incomeId)
    {
        $this->id = $incomeId;
        $this->supplier = new OperandRelation();
        $this->accruedBy = new UserRelation();
        $this->incomeParts = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @deprecated for BC with TenantListener
     */
    public function getId(): string
    {
        return $this->toId()->toString();
    }

    public function toId(): IncomeId
    {
        return $this->id;
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
