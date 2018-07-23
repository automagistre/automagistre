<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

/**
 * @ORM\Entity
 */
class Payment
{
    use Identity;
    use CreatedAt;

    /**
     * @var Operand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand")
     * @ORM\JoinColumn
     */
    private $recipient;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $subtotal;

    public function __construct(Operand $recipient, string $description, Money $money, Money $subtotal)
    {
        $this->recipient = $recipient;
        $this->description = $description;
        $this->amount = $money->getAmount();
        $this->subtotal = $subtotal->getAmount();
    }

    public function getRecipient(): Operand
    {
        return $this->recipient;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAmount(): Money
    {
        return new Money($this->amount, new Currency('RUB'));
    }

    public function getSubtotal(): ?Money
    {
        if (null === $this->subtotal) {
            return null;
        }

        return new Money($this->subtotal, new Currency('RUB'));
    }
}
