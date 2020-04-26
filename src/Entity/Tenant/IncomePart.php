<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Price;
use App\Doctrine\ORM\Mapping\Traits\Quantity;
use App\Entity\Embeddable\PartRelation;
use App\Entity\Landlord\Part;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Money\Money;
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
     * @var Income|null
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity=Income::class, inversedBy="incomeParts")
     */
    private $income;

    /**
     * @var PartRelation
     *
     * @Assert\Valid
     *
     * @ORM\Embedded(class=PartRelation::class)
     */
    private $part;

    /**
     * @var MotionIncome|null
     *
     * @ORM\ManyToOne(targetEntity=MotionIncome::class)
     */
    private $accruedMotion;

    public function __construct()
    {
        $this->part = new PartRelation();
    }

    public function accrue(MotionIncome $motion): void
    {
        $this->accruedMotion = $motion;
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

    public function getPart(): ?Part
    {
        return $this->part->entityOrNull();
    }

    public function setPart(?Part $part): void
    {
        $this->part = new PartRelation($part);
    }
}
