<?php

declare(strict_types=1);

namespace App\Wallet\Entity;

use App\Tenant\Entity\TenantEntity;
use App\Wallet\Enum\WalletTransactionSource;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 * @ORM\Table(name="wallet_transaction")
 */
class WalletTransaction extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    private WalletTransactionId $id;

    /**
     * @ORM\Column
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
        WalletTransactionId $id,
        WalletId $walletId,
        Money $amount,
        WalletTransactionSource $source,
        UuidInterface $sourceId,
        ?string $description,
    ) {
        $this->id = $id;
        $this->walletId = $walletId;
        $this->amount = $amount;
        $this->source = $source;
        $this->sourceId = $sourceId;
        $this->description = $description;
    }
}
