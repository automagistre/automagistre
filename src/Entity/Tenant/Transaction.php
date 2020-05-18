<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Shared\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Shared\Doctrine\ORM\Mapping\Traits\CreatedByRelation;
use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
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
     * @ORM\Column(type="text", length=512)
     */
    protected string $description;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    protected Money $amount;

    public function __construct(string $description, Money $money)
    {
        $this->description = $description;
        $this->amount = $money;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }
}
