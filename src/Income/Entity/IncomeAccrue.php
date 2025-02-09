<?php

declare(strict_types=1);

namespace App\Income\Entity;

use App\Income\Event\IncomeAccrued;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity(readOnly=true)
 */
class IncomeAccrue extends TenantEntity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column
     */
    public UuidInterface $id;

    /**
     * @ORM\OneToOne(targetEntity=Income::class, inversedBy="accrue")
     */
    public Income $income;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    public Money $amount;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(Income $income, Money $amount)
    {
        $this->id = Uuid::uuid6();
        $this->income = $income;
        $this->amount = $amount;

        $this->record(new IncomeAccrued($income->toId()));
    }
}
