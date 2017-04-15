<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UuidGenerator extends AbstractIdGenerator
{
    /**
     * Generate an identifier.
     *
     * @param EntityManager $em
     * @param Entity        $entity
     *
     * @return UuidInterface
     */
    public function generate(EntityManager $em, $entity)
    {
        return Uuid::uuid1();
    }
}
