<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Price;
use App\Doctrine\ORM\Mapping\Traits\Quantity;
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Income", inversedBy="incomeParts")
     */
    private $income;

    /**
     * @var Part|null
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Part")
     */
    private $part;

    /**
     * @var Supply|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Supply")
     */
    private $supply;

    public static function fromSupply(Supply $supply): self
    {
        $incomePart = new self();
        $incomePart->part = $supply->getPart();
        $incomePart->quantity = $supply->getQuantity();
        $incomePart->price = $supply->getPrice();
        $incomePart->supply = $supply;

        return $incomePart;
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
        return $this->part;
    }

    public function setPart(?Part $part): void
    {
        $this->part = $part;
    }

    public function getSupply(): ?Supply
    {
        return $this->supply;
    }

    public function setSupply(?Supply $supply): void
    {
        $this->supply = $supply;
    }
}
