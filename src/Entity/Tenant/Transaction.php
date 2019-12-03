<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedByRelation;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\MappedSuperclass
 */
abstract class Transaction
{
    use Identity;
    use CreatedAt;
    use CreatedByRelation;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    protected $description;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="Money\Money")
     */
    protected $amount;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="Money\Money")
     */
    protected $subtotal;

    public function __construct(string $description, Money $money, Money $subtotal, User $user)
    {
        $this->description = $description;
        $this->amount = $money;
        $this->subtotal = $subtotal;
        $this->setCreatedBy($user);
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
