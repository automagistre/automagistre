<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use LogicException;
use function sprintf;

/**
 * @property int|null $id
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
    }

    abstract public static function entityClass(): string;

    public function isEmpty(): bool
    {
        return null === $this->id;
    }

    public function id(): int
    {
        if (null === $this->id) {
            throw new LogicException('ID is null, are you use isEmpty() first?');
        }

        return $this->id;
    }

    public function entity(): object
    {
        if (null === $this->entity) {
            throw new LogicException(
                sprintf('Entity in %s is null, are you use isEmpty() first?', static::class)
            );
        }

        return $this->entity;
    }

    public function entityOrNull(): ?object
    {
        return $this->entity;
    }
}
