<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Customer\Enum\CustomerTransactionSource;
use App\Shared\Identifier\Identifier;
use App\User\Entity\UserId;
use function assert;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use function is_subclass_of;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="customer_transaction_view")
 */
class CustomerTransactionView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="customer_transaction_id")
     */
    public CustomerTransactionId $id;

    /**
     * @ORM\Column(type="operand_id")
     */
    public OperandId $operandId;

    /**
     * @ORM\Column(type="money")
     */
    public Money $amount;

    /**
     * @ORM\Column(type="operand_transaction_source")
     */
    public CustomerTransactionSource $source;

    /**
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $sourceId;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    public ?string $description;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    public ?DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="user_id")
     */
    public ?UserId $createdBy;

    private function __construct(
        CustomerTransactionId $id,
        OperandId $operandId,
        Money $amount,
        CustomerTransactionSource $source,
        UuidInterface $sourceId,
        ?string $description,
        ?DateTimeImmutable $createdAt,
        ?UserId $createdBy
    ) {
        $this->id = $id;
        $this->operandId = $operandId;
        $this->amount = $amount;
        $this->source = $source;
        $this->sourceId = $sourceId;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->createdBy = $createdBy;
    }

    public function toId(): CustomerTransactionId
    {
        return $this->id;
    }

    public static function sql(): string
    {
        return '
                CREATE VIEW customer_transaction_view AS
                SELECT
                    ct.id,
                    ct.operand_id,
                    CONCAT(ct.amount_currency_code, \' \', ct.amount_amount) AS amount,
                    ct.source,
                    CASE
                        WHEN
                            ct.source IN (
                            '.CustomerTransactionSource::payroll()->toId().',
                            '.CustomerTransactionSource::manual()->toId().'
                            )
                        THEN
                            wt.wallet_id
                        ELSE
                            ct.source_id
                    END,
                    ct.description,
                    cb.created_at,
                    cb.user_id AS created_by
                FROM customer_transaction ct
                JOIN created_by cb ON cb.id = ct.id
                LEFT JOIN wallet_transaction wt ON wt.id = ct.source_id
            ';
    }

    public function toSourceIdentifier(): Identifier
    {
        $class = $this->source->toSourceClass();

        assert(is_subclass_of($class, Identifier::class));

        return Identifier::fromClass($class, $this->sourceId);
    }
}
