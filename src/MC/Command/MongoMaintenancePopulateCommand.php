<?php

declare(strict_types=1);

namespace App\MC\Command;

use App\Manufacturer\Documents\Manufacturer;
use App\Manufacturer\Entity\ManufacturerId;
use App\MC\Documents\Maintenance;
use App\MC\Documents\McPart;
use App\MC\Documents\Work;
use App\MC\Entity\McEquipment;
use App\Part\Documents\Part;
use App\Part\Entity\PartId;
use App\Shared\Doctrine\Registry;
use App\Shared\Money\Documents\Money;
use App\Vehicle\Documents\Vehicle;
use App\Vehicle\Entity\Embedded\Engine;
use App\Vehicle\Entity\VehicleId;
use function array_key_exists;
use function array_map;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MongoMaintenancePopulateCommand extends Command
{
    protected static $defaultName = 'mongo:maintenance:populate';

    private Registry $registry;

    private ManagerRegistry $odmRegistry;

    private static array $cache = [];

    public function __construct(Registry $registry, ManagerRegistry $odmRegistry)
    {
        parent::__construct(null);

        $this->registry = $registry;
        $this->odmRegistry = $odmRegistry;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dm = $this->odmRegistry->getManager('www');

        $equipments = $this->registry->manager(McEquipment::class)
            ->createQueryBuilder()
            ->select('mc_equipment', 'model', 'lines', 'work', 'parts')
            ->from(McEquipment::class, 'mc_equipment')
            ->leftJoin('mc_equipment.model', 'model')
            ->leftJoin('mc_equipment.lines', 'lines')
            ->leftJoin('lines.work', 'work')
            ->leftJoin('lines.parts', 'parts')
            ->orderBy('mc_equipment.id', 'ASC')
            ->getQuery()
            ->getArrayResult();

        foreach ($equipments as $item) {
            $dm->persist(
                new Maintenance(
                    $item['id'],
                    $this->createVehicle($item['model']['id']),
                    new Engine(
                        $item['equipment.engine.name'],
                        $item['equipment.engine.type'],
                        $item['equipment.engine.airIntake'],
                        $item['equipment.engine.injection'],
                        $item['equipment.engine.capacity'],
                    ),
                    $item['equipment.transmission'],
                    $item['equipment.wheelDrive'],
                    array_map(
                        fn (array $line) => new Work(
                            $line['work']['name'],
                            $line['work']['description'],
                            $line['period'],
                            $line['recommended'],
                            new Money(
                                $line['work']['price.amount'],
                                $line['work']['price.currency.code'],
                            ),
                            array_map(
                                fn (array $part) => new McPart(
                                    $this->createPart($part['partId']),
                                    $part['quantity'],
                                    $part['recommended'],
                                ),
                                $line['parts']
                            ),
                        ),
                        $item['lines'],
                    ),
                ),
            );
        }

        $dm->flush();

        return 0;
    }

    private function createVehicle(VehicleId $vehicleId): Vehicle
    {
        if (array_key_exists($vehicleId->toString(), self::$cache)) {
            return self::$cache[$vehicleId->toString()];
        }

        $view = $this->registry->view($vehicleId);

        return self::$cache[$vehicleId->toString()] = new Vehicle(
            $vehicleId,
            $this->createManufacturer($view['manufacturerId']),
            $view['name'],
            $view['localizedName'],
            $view['caseName'],
            $view['yearFrom'],
            $view['yearTill'],
        );
    }

    private function createPart(PartId $partId): Part
    {
        if (array_key_exists($partId->toString(), self::$cache)) {
            return self::$cache[$partId->toString()];
        }

        $view = $this->registry->view($partId);

        return self::$cache[$partId->toString()] = new Part(
            $partId,
            $this->createManufacturer($view['manufacturerId']),
            $view['name'],
            $view['number'],
            $view['universal'],
            new Money('0', 'RUB'),
            new Money('0', 'RUB'),
        );
    }

    private function createManufacturer(ManufacturerId $manufacturerId): Manufacturer
    {
        if (array_key_exists($manufacturerId->toString(), self::$cache)) {
            return self::$cache[$manufacturerId->toString()];
        }

        $view = $this->registry->view($manufacturerId);

        return self::$cache[$manufacturerId->toString()] = new Manufacturer(
            $manufacturerId,
            $view['name'],
            $view['localizedName'],
        );
    }
}
