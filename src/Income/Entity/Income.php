<?php

declare(strict_types=1);

namespace App\Income\Entity;

use App\Customer\Entity\OperandId;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Tenant\Entity\TenantEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

/**
 * @ORM\Entity
 */
class Income extends TenantEntity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column
     */
    private IncomeId $id;

    /**
     * @ORM\Column
     */
    private OperandId $supplierId;

    /**
     * @var null|string
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
     * @ORM\OneToOne(targetEntity=IncomeAccrue::class, mappedBy="income", cascade={"persist"})
     */
    private ?IncomeAccrue $accrue;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $oldId = null;

    public function __construct(IncomeId $incomeId, OperandId $supplierId, ?string $document)
    {
        $this->id = $incomeId;
        $this->supplierId = $supplierId;
        $this->document = $document;
        $this->incomeParts = new ArrayCollection();
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

    public function getSupplierId(): OperandId
    {
        return $this->supplierId;
    }

    public function setSupplierId(OperandId $supplierId): void
    {
        $this->supplierId = $supplierId;
    }

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(?string $document): void
    {
        $this->document = $document;
    }

    public function isEditable(): bool
    {
        return null === $this->accrue;
    }

    /**
     * @return IncomePart[]
     */
    public function getIncomeParts(): array
    {
        return $this->incomeParts->toArray();
    }

    public function itemsCount(): int
    {
        return $this->incomeParts->count();
    }

    public function accrue(): void
    {
        $this->accrue = new IncomeAccrue($this, $this->getTotalPrice());
    }

    public function isAccrued(): bool
    {
        return null !== $this->accrue;
    }

    public function getAccrue(): ?IncomeAccrue
    {
        return $this->accrue;
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
