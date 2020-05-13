<?php

declare(strict_types=1);

namespace App\Car\Repository;

use App\Car\Entity\Car;
use App\Car\Entity\CarId;
use App\Car\Entity\CarPossession;
use App\Customer\Domain\Operand;
use App\Customer\Domain\OperandId;
use App\Doctrine\Registry;
use function array_map;

final class CarPossessionRepository
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function carsByPossessor(OperandId $operandId): array
    {
        $ids = $this->registry->connection(CarPossession::class)
            ->fetchAll('
                SELECT DISTINCT cp.car_id 
                FROM car_possession cp
                WHERE cp.possessor_id = :possessor
                GROUP BY cp.car_id
                HAVING COUNT(cp.transition) > 0
            ',
                [
                    'possessor' => $operandId,
                ]);

        return $this->registry->viewListBy(Car::class, ['uuid' => array_map('array_shift', $ids)]);
    }

    public function possessorsByCar(CarId $carId): array
    {
        $ids = $this->registry->connection(CarPossession::class)
            ->fetchAll('
                SELECT DISTINCT cp.possessor_id 
                FROM car_possession cp
                WHERE cp.car_id = :car
                GROUP BY cp.possessor_id
                HAVING COUNT(cp.transition) > 0
            ',
                [
                    'car' => $carId,
                ]);

        return $this->registry->viewListBy(Operand::class, ['uuid' => array_map('array_shift', $ids)]);
    }
}
