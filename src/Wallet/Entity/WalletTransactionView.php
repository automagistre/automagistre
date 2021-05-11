<?php

declare(strict_types=1);

namespace App\Wallet\Entity;

use Premier\Identifier\Identifier;
use App\User\Entity\UserId;
use App\Wallet\Enum\WalletTransactionSource;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\UuidInterface;
use function assert;
use function is_subclass_of;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="wallet_transaction_view")
 *
 * @psalm-suppress MissingConstructor
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
