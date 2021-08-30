<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\CreatedBy\Entity\Blamable;
use App\Customer\Enum\CustomerTransactionSource;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Premier\Identifier\Identifier;
use Ramsey\Uuid\UuidInterface;
use function assert;
use function is_subclass_of;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="customer_transaction_view")
 *
 * @psalm-suppress MissingConstructor
 */
class CustomerTransactionView extends TenantEntity
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
     * @ORM\Embedded(class=Blamable::class)
     */
    public Blamable $created;

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
