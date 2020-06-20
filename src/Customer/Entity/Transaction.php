<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Customer\Enum\TransactionSource;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\UuidInterface;
use SimpleBus\Message\Recorder\ContainsRecordedMessages;
use SimpleBus\Message\Recorder\PrivateMessageRecorderCapabilities;

/**
 * @ORM\Entity
 * @ORM\Table(name="customer_transaction")
 *
 * @psalm-immutable
 * @psalm-internal App\Customer\Entity
 */
class Transaction implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id()
     * @ORM\Column(type="customer_transaction_id")
     */
    private TransactionId $id;

    /**
     * @ORM\Column(type="operand_id")
     */
    private OperandId $operandId;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    private Money $amount;

    /**
     * @ORM\Column(type="operand_transaction_source")
     */
    private TransactionSource $source;

    /**
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $sourceId;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $description;

    public function __construct(
        TransactionId $id,
        OperandId $operandId,
        Money $amount,
        TransactionSource $source,
        UuidInterface $sourceId,
        ?string $description
    ) {
        $this->id = $id;
        $this->operandId = $operandId;
        $this->amount = $amount;
        $this->source = $source;
        $this->sourceId = $sourceId;
        $this->description = $description;
    }
}
