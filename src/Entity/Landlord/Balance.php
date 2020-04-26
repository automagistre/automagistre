<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Customer\Domain\Operand;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Price;
use App\Tenant\Tenant;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"operand_id", "tenant"})
 *     }
 * )
 */
class Balance
{
    use Identity;
    use Price {
        getPrice as _;
    }

    /**
     * @ORM\ManyToOne(targetEntity=Operand::class)
     */
    private Operand $operand;

    /**
     * @ORM\Column(type="tenant_enum")
     */
    private Tenant $tenant;

    public function __construct(Operand $operand, Tenant $tenant, Money $balance)
    {
        $this->operand = $operand;
        $this->tenant = $tenant;
        $this->price = $balance;
    }
}
