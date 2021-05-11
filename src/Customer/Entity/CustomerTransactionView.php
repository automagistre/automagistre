<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Customer\Enum\CustomerTransactionSource;
use Premier\Identifier\Identifier;
use App\User\Entity\UserId;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\UuidInterface;
use function assert;
use function is_subclass_of;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="customer_transaction_view")
 *
 * @psalm-suppress MissingConstructor
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

    public function toId(): CustomerTransactionId
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
