<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Entity\Landlord\Operand;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Embeddable
 *
 * @method Operand entity()
 */
final class OperandRelation extends Relation
{
    /**
     * @var UuidInterface|null
     *
     * @ORM\Column(type="uuid_binary")
     */
    private $uuid;

    public function __construct(Operand $entity = null)
    {
        $this->uuid = null !== $entity ? $entity->uuid() : null;

        parent::__construct($entity);
    }

    public function uuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function class(): string
    {
        return Operand::class;
    }
}
