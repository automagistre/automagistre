<?php

declare(strict_types=1);

namespace App\Command\Part;

use App\Doctrine\Registry;
use App\Entity\Landlord\Car;
use App\Entity\Landlord\CarModel;
use App\Entity\Landlord\Part;
use App\Entity\Landlord\PartCase;
use App\Entity\Tenant\OrderItemPart;
use function array_flip;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_unique;
use function count;
use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use function sprintf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartCaseFindCommand extends Command
{
    protected static $defaultName = 'part:case:find';

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        parent::__construct();

        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $cars = $this->getCars();

        $data = $this->registry->repository(OrderItemPart::class)
            ->createQueryBuilder('entity')
            ->select('entity.part.id AS part_id, orders.car.id AS car_id')
            ->join('entity.order', 'orders')
            ->where('orders.car.id IN (:cars)')
            ->andWhere('entity.part.id IS NOT NULL')
            ->setParameter('cars', array_keys($cars))
            ->getQuery()
            ->getScalarResult();
        $universal = $this->getUniversal(array_map(static function (array $item): string {
            return $item['part_id'];
        }, $data));

        $progress = $io->createProgressBar(count($data));
        $em = $this->registry->manager(PartCase::class);

        $map = $this->getMap();

        $persisted = 0;
        foreach (SimpleBatchIteratorAggregate::fromArrayResult($data, $em, 300) as $item) {
            /* @noinspection DisconnectedForeachInstructionInspection */
            $progress->advance();

            ['part_id' => $partId, 'car_id' => $carId] = $item;
            $carModelId = $cars[$carId];

            $hash = $partId.$carModelId;
            if (array_key_exists($partId, $universal) || array_key_exists($hash, $map)) {
                continue;
            }

            $map[$hash] = true;

            /** @var Part $part */
            $part = $em->getReference(Part::class, $partId);
            /** @var CarModel $carModel */
            $carModel = $em->getReference(CarModel::class, $carModelId);

            $em->persist(new PartCase($part, $carModel));
            ++$persisted;
        }

        $progress->finish();

        $io->success(sprintf('Persisted %s.', $persisted));

        return 0;
    }

    private function getUniversal(array $partIds): array
    {
        $data = $this->registry->repository(Part::class)
            ->createQueryBuilder('entity')
            ->select('entity.id AS part_id')
            ->where('entity.id IN (:parts)')
            ->andWhere('entity.universal = :universal')
            ->setParameter('parts', array_unique($partIds))
            ->setParameter('universal', true)
            ->getQuery()
            ->getScalarResult();

        return array_flip(array_map('array_pop', $data));
    }

    private function getMap(): array
    {
        $data = $this->registry->repository(PartCase::class)
            ->createQueryBuilder('part_case')
            ->select('CONCAT_WS(\'\', part_case.part, part_case.carModel)')
            ->getQuery()
            ->getScalarResult();

        return array_flip(array_map('array_pop', $data));
    }

    private function getCars(): array
    {
        $data = $this->registry->repository(Car::class)
            ->createQueryBuilder('car')
            ->select(['car.id AS car_id', 'carModel.id AS car_model_id'])
            ->join('car.carModel', 'carModel')
            ->where('carModel.caseName IS NOT NULL')
            ->getQuery()
            ->getScalarResult();

        $cars = [];
        foreach ($data as $item) {
            $cars[$item['car_id']] = $item['car_model_id'];
        }

        return $cars;
    }
}
