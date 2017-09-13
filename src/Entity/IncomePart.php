<?php

declare(strict_types=1);

namespace App\Entity;

use App\Uuid\UuidGenerator;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class IncomePart
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     */
    private $id;

    /**
     * @var Income
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Income", inversedBy="incomeParts")
     */
    private $income;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Part")
     */
    private $part;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(length=3)
     */
    private $currency;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function __construct(Income $income, Part $part, Money $price, int $quantity)
    {
        $this->id = UuidGenerator::generate();

        $this->income = $income;
        $this->part = $part;
        $this->price = (int) $price->getAmount();
        $this->currency = $price->getCurrency()->getCode();
        $this->quantity = $quantity;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getIncome(): Income
    {
        return $this->income;
    }

    public function getPart(): Part
    {
        return $this->part;
    }

    public function getPrice(): Money
    {
        return new Money($this->price, new Currency($this->currency));
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTotalPrice(): Money
    {
        return $this->getPrice()->multiply($this->getQuantity() / 100);
    }
}
