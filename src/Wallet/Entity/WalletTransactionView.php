<?php

declare(strict_types=1);

namespace App\Wallet\Entity;

use App\Shared\Identifier\Identifier;
use App\User\Entity\UserId;
use App\Wallet\Enum\WalletTransactionSource;
use function assert;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use function is_subclass_of;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="wallet_transaction_view")
 */
class WalletTransactionView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="wallet_transaction_id")
     */
    public WalletTransactionId $id;

    /**
     * @ORM\Column(type="wallet_id")
     */
    public WalletId $walletId;

    /**
     * @ORM\Column(type="money")
     */
    public Money $amount;

    /**
     * @ORM\Column(type="wallet_transaction_source")
     */
    public WalletTransactionSource $source;

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
        WalletTransactionId $id,
        WalletId $walletId,
        Money $amount,
        WalletTransactionSource $source,
        UuidInterface $sourceId,
        ?string $description,
        ?DateTimeImmutable $createdAt,
        ?UserId $createdBy
    ) {
        $this->id = $id;
        $this->walletId = $walletId;
        $this->amount = $amount;
        $this->source = $source;
        $this->sourceId = $sourceId;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->createdBy = $createdBy;
    }

    public function toId(): WalletTransactionId
    {
        return $this->id;
    }

    public static function sql(): string
    {
        return '
                CREATE VIEW wallet_transaction_view AS
                SELECT
                    wt.id,
                    wt.wallet_id,
                    CONCAT(wt.amount_currency_code, \' \', wt.amount_amount) AS amount,
                    wt.source,
                    CASE
                        WHEN
                            wt.source IN (
                            '.WalletTransactionSource::payroll()->toId().',
                            '.WalletTransactionSource::operandManual()->toId().'
                            )
                            THEN ct.operand_id
                        ELSE
                            wt.source_id
                    END,
                    wt.description,
                    cb.created_at,
                    cb.user_id AS created_by
                FROM wallet_transaction wt
                JOIN created_by cb ON cb.id = wt.id
                LEFT JOIN customer_transaction ct ON ct.id = wt.source_id
            ';
    }

    public function toSourceIdentifier(): Identifier
    {
        $class = $this->source->toSourceClass();

        assert(is_subclass_of($class, Identifier::class));

        return Identifier::fromClass($class, $this->sourceId);
    }
}
