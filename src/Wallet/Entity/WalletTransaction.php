<?php

declare(strict_types=1);

namespace App\Wallet\Entity;

use App\Wallet\Enum\WalletTransactionSource;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="wallet_transaction")
 */
class WalletTransaction
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="wallet_transaction_id")
     */
    private WalletTransactionId $id;

    /**
     * @ORM\Column(type="wallet_id")
     */
    private WalletId $walletId;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    private Money $amount;

    /**
     * @ORM\Column(type="wallet_transaction_source")
     */
    private WalletTransactionSource $source;

    /**
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $sourceId;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $description;

    public function __construct(
        WalletTransactionId $id,
        WalletId $walletId,
        Money $amount,
        WalletTransactionSource $source,
        UuidInterface $sourceId,
        ?string $description
    ) {
        $this->id = $id;
        $this->walletId = $walletId;
        $this->amount = $amount;
        $this->source = $source;
        $this->sourceId = $sourceId;
        $this->description = $description;
    }
}
