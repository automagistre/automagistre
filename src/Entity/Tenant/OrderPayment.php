<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedByRelation as CreatedBy;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class OrderPayment
{
    use Identity;
    use CreatedAt;
    use CreatedBy;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="payments")
     */
    private Order $order;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    private Money $money;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $description = null;

    public function __construct(Order $order, Money $money, ?string $description)
    {
        $this->order = $order;
        $this->money = $money;
        $this->description = $description;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
