<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

/**
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

    abstract public function class(): string;

    public function isEmpty(): bool
    {
        return null === $this->entity;
    }

    public function entity(): object
    {
        return $this->entity;
    }
}
