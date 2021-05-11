<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Income\Entity\IncomePartId;
use App\Order\Entity\OrderId;
use App\Storage\Enum\MotionType;
use App\User\Entity\UserId;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Premier\Identifier\Identifier;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Embeddable()
 *
 * @psalm-immutable
 */
final class MotionSource
{
    /**
     * @ORM\Column(type="motion_source_enum")
     */
    public MotionType $type;

    /**
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    public function __construct(MotionType $type, UuidInterface $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    public static function manual(UserId $id): self
    {
        return new self(MotionType::manual(), $id->toUuid());
    }

    public static function order(OrderId $id): self
    {
        return new self(MotionType::order(), $id->toUuid());
    }

    public static function income(IncomePartId $id): self
    {
        return new self(MotionType::income(), $id->toUuid());
    }

    public static function inventorization(InventorizationId $id): self
    {
        return new self(MotionType::inventorization(), $id->toUuid());
    }

    public function toIdentifier(): Identifier
    {
        return match ($this->type) {
            MotionType::income() => IncomePartId::from($this->id),
            MotionType::order() => OrderId::from($this->id),
            MotionType::manual() => UserId::from($this->id),
            default => throw new LogicException('Unexpected type: '.$this->type->toId()),
        };
    }
}
