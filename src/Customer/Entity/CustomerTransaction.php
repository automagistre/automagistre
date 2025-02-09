<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Customer\Enum\CustomerTransactionSource;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 * @ORM\Table(name="customer_transaction")
 *
 * @psalm-immutable
 */
class CustomerTransaction extends TenantEntity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column
     */
    private CustomerTransactionId $id;

    /**
     * @ORM\Column
     */
    private OperandId $operandId;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    private Money $amount;

    /**
     * @ORM\Column(type="operand_transaction_source")
     */
    private CustomerTransactionSource $source;

    /**
     * @ORM\Column
     */
    private UuidInterface $sourceId;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(
        CustomerTransactionId $id,
        OperandId $operandId,
        Money $amount,
        CustomerTransactionSource $source,
        UuidInterface $sourceId,
        ?string $description,
    ) {
        $this->id = $id;
        $this->operandId = $operandId;
        $this->amount = $amount;
        $this->source = clone $source;
        $this->sourceId = $sourceId;
        $this->description = $description;
    }
}
