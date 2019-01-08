<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use Ramsey\Uuid\UuidInterface;

/**
 * @property UuidInterface $uuid
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

    public function uuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function isEmpty(): bool
    {
        return null === $this->uuid;
    }

    public function entity(): object
    {
        return $this->entity;
    }
}
