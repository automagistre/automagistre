<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedByRelation;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Event\PaymentCreated;
use App\Infrastructure\DomainEvents\RaiseEventsInterface;
use App\Infrastructure\DomainEvents\RaiseEventsTrait;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\MappedSuperclass
 */
abstract class Transaction implements RaiseEventsInterface
{
    use Identity;
    use CreatedAt;
    use CreatedByRelation;
    use RaiseEventsTrait;

    /**
     * @ORM\Column(type="text", length=512)
     */
    protected string $description;

    /**
     * @ORM\Embedded(class="Money\Money")
     */
    protected Money $amount;

    public function __construct(string $description, Money $money)
    {
        $this->description = $description;
        $this->amount = $money;

        $this->raise(new PaymentCreated($this));
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
