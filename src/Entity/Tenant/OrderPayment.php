<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedByRelation as CreatedBy;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\User\Entity\User;
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenant\Order", inversedBy="payments")
     */
    private Order $order;

    /**
     * @ORM\Embedded(class="Money\Money")
     */
    private Money $money;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $description = null;

    public function __construct(Order $order, Money $money, ?string $description, User $user)
    {
        $this->order = $order;
        $this->money = $money;
        $this->description = $description;
        $this->setCreatedBy($user);
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
