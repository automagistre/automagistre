<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedBy;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Income
{
    use Identity;
    use CreatedAt;
    use CreatedBy;

    /**
     * @var Operand
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand")
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
     * @ORM\OneToMany(targetEntity="App\Entity\IncomePart", mappedBy="income",
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
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $accruedBy;

    public function __construct()
    {
        $this->incomeParts = new ArrayCollection();
    }

    public function __toString(): string
    {
        return \sprintf('№%s %s от %s', $this->getId(), $this->getSupplier(), $this->getCreatedAt()->format('d.m.Y'));
    }

    public function isEditable(): bool
    {
        return null === $this->accruedAt;
    }

    public function getSupplier(): ?Operand
    {
        return $this->supplier;
    }

    public function setSupplier(?Operand $supplier): void
    {
        $this->supplier = $supplier;
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

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function itemsCount(): int
    {
        return $this->incomeParts->count();
    }

    public function accrue(User $user): void
    {
        $this->accruedBy = $user;
        $this->accruedAt = new DateTimeImmutable();
    }

    public function getAccruedAt(): ?DateTimeImmutable
    {
        return $this->accruedAt;
    }

    public function getAccruedBy(): ?User
    {
        return $this->accruedBy;
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
