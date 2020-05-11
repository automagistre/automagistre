<?php

declare(strict_types=1);

namespace App\Income\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Price;
use App\Doctrine\ORM\Mapping\Traits\Quantity;
use App\Part\Domain\PartId;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Money\Money;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class IncomePart
{
    use Identity;
    use Price;
    use Quantity;

    /**
     * @ORM\Column(type="part_id")
     */
    public ?PartId $partId = null;

    /**
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $uuid;

    /**
     * @var Income|null
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity=Income::class, inversedBy="incomeParts")
     */
    private $income;

    public function __construct()
    {
        $this->uuid = IncomeId::generate()->toUuid();
    }

    public function toId(): UuidInterface
    {
        return $this->uuid;
    }

    public function setPrice(Money $price): void
    {
        $this->price = $price;
    }

    public function getTotalPrice(): Money
    {
        return $this->getPrice()->multiply($this->quantity / 100);
    }

    public function getIncome(): ?Income
    {
        return $this->income;
    }

    public function setIncome(?Income $income): void
    {
        if (null !== $this->income) {
            throw new LogicException('Income already defined.');
        }

        $this->income = $income;
    }
}
