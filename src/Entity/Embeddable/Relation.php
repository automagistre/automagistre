<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use LogicException;
use Ramsey\Uuid\UuidInterface;

/**
 * @property UuidInterface|null $uuid
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class Relation
{
    /**
     * @var object|null
     */
    private $entity;

    public function __construct(object $entity = null)
    {
        $this->entity = $entity;

        if (null !== $entity) {
            if (!\method_exists($entity, 'uuid')) {
                throw new LogicException(\sprintf('Method "%s::uuid()" not exist', \get_class($entity)));
            }

            $this->uuid = $entity->uuid();
        }
    }

    abstract public static function entityClass(): string;

    public function isEmpty(): bool
    {
        return null === $this->uuid;
    }

    public function uuid(): UuidInterface
    {
        if (null === $this->uuid) {
            throw new LogicException('Uuid is null, are you use isEmpty() first?');
        }

        return $this->uuid;
    }

    public function entity(): object
    {
        if (null === $this->entity) {
            throw new LogicException('Entity is null, are you use isEmpty() first?');
        }

        return $this->entity;
    }

    public function entityOrNull(): ?object
    {
        return $this->entity;
    }
}
