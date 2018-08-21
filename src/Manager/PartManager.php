<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Motion;
use App\Entity\Part;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartManager
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function inStock(Part $part): int
    {
        $em = $this->registry->getEntityManager();

        return (int) $em->createQueryBuilder()
            ->select('SUM(entity.quantity)')
            ->from(Motion::class, 'entity')
            ->groupBy('entity.part')
            ->where('entity.part = :part')
            ->setParameter('part', $part)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
