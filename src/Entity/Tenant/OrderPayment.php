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
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenant\Order", inversedBy="payments")
     */
    private $order;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="Money\Money")
     */
    private $money;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $description;

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
