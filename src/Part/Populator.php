<?php

declare(strict_types=1);

namespace App\Part;

use App\Entity\Landlord\Manufacturer;
use App\Entity\Landlord\Part;
use App\Partner\Ixora\Finder;
use function array_filter;
use function count;
use Doctrine\ORM\EntityManager;
use function strpos;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Populator
{
    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(Finder $finder, EntityManager $em)
    {
        $this->finder = $finder;
        $this->em = $em;
    }

    /**
     * @return Part[]
     */
    public function populate(string $number): array
    {
        $em = $this->em;
        $manufacturerRepository = $em->getRepository(Manufacturer::class);
        $partRepository = $em->getRepository(Part::class);

        $partQuery = $partRepository->createQueryBuilder('part')
            ->leftJoin('part.manufacturer', 'manufacturer')
            ->where('part.number = :part')
            ->andWhere('manufacturer.name = :manufacturer')
            ->getQuery();

        $parts = [];
        foreach ($this->finder->search($number) as $model) {
            $exists = $partQuery
                ->setParameter('manufacturer', $model->manufacturer)
                ->setParameter('part', $model->number)
                ->getOneOrNullResult();

            if ($exists instanceof Part) {
                continue;
            }

            $manufacturer = $manufacturerRepository->findOneBy(['name' => $model->manufacturer]);
            if (!$manufacturer instanceof Manufacturer) {
                $manufacturer = new Manufacturer();
                $manufacturer->setName($model->manufacturer);
                $em->persist($manufacturer);
            }

            $part = new Part();
            $part->setManufacturer($manufacturer);
            $part->setName($model->name);
            $part->setNumber($model->number);
            $em->persist($part);

            $parts[] = $part;
        }

        if (0 < count($parts)) {
            $em->flush();
        }

        return array_filter($parts, function (Part $part) use ($number) {
            return false !== strpos((string) $part->getNumber(), $number);
        });
    }
}
