<?php

declare(strict_types=1);

namespace App\Part;

use App\Entity\Manufacturer;
use App\Entity\Part;
use App\Partner\Ixora\Finder;
use Doctrine\ORM\EntityManager;

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
     * @param string $number
     *
     * @return Part[]
     */
    public function populate(string $number): array
    {
        $manufacturerRepository = $this->em->getRepository(Manufacturer::class);
        $partRepository = $this->em->getRepository(Part::class);

        $partQuery = $partRepository->createQueryBuilder('part')
            ->leftJoin('part.manufacturer', 'manufacturer')
            ->where('part.number = :part')
            ->andWhere('manufacturer.name = :manufacturer')
            ->getQuery();

        $parts = [];
        foreach ($this->finder->search($number) as $model) {
            $exists = $partQuery
                ->setParameters(['manufacturer' => $model->manufacturer, 'part' => $model->number])
                ->getOneOrNullResult();

            if ($exists) {
                continue;
            }

            $manufacturer = $manufacturerRepository->findOneBy(['name' => $model->manufacturer]);
            if (!$manufacturer) {
                $manufacturer = new Manufacturer();
                $manufacturer->setName($model->manufacturer);
                $this->em->persist($manufacturer);
            }

            $part = new Part();
            $part->setManufacturer($manufacturer);
            $part->setName($model->name);
            $part->setNumber($model->number);
            $this->em->persist($part);

            $parts[] = $part;
        }

        if ($parts) {
            $this->em->flush();
        }

        return array_filter($parts, function (Part $part) use ($number) {
            return false !== strpos((string) $part->getNumber(), $number);
        });
    }
}
