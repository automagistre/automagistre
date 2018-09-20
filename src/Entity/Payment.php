<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
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
     * @var Money
     *
     * @ORM\Embedded(class="Money\Money")
     */
    private $amount;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="Money\Money")
     */
    private $subtotal;

    public function __construct(Operand $recipient, string $description, Money $money, Money $subtotal)
    {
        $this->recipient = $recipient;
        $this->description = $description;
        $this->amount = $money;
        $this->subtotal = $subtotal;
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
        return $this->amount;
    }

    public function getSubtotal(): Money
    {
        return $this->subtotal;
    }
}
