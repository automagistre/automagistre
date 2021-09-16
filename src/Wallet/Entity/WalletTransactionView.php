<?php

declare(strict_types=1);

namespace App\Wallet\Entity;

use App\CreatedBy\Entity\Blamable;
use App\Tenant\Entity\TenantEntity;
use App\Wallet\Enum\WalletTransactionSource;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Premier\Identifier\Identifier;
use Ramsey\Uuid\UuidInterface;
use function assert;
use function is_subclass_of;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="wallet_transaction_view")
 *
 * @psalm-suppress MissingConstructor
 */
class WalletTransactionView extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public WalletTransactionId $id;

    /**
     * @ORM\Column
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
     * @ORM\Column
     */
    public UuidInterface $sourceId;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    public ?string $description;

    /**
     * @ORM\Embedded(class=Blamable::class)
     */
    public Blamable $created;

    public function toId(): WalletTransactionId
    {
        return $this->id;
    }

    public function toSourceIdentifier(): Identifier
    {
        $class = $this->source->toSourceClass();

        assert(is_subclass_of($class, Identifier::class));

        return new $class($this->sourceId);
    }
}
