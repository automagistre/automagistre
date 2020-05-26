<?php

declare(strict_types=1);

namespace App\Car\Repository;

use App\Car\Entity\Car;
use App\Car\Entity\CarId;
use App\Customer\Entity\Operand;
use App\Customer\Entity\OperandId;
use App\Shared\Doctrine\Registry;
use function array_map;

final class CarCustomerRepository
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function carsByCustomer(OperandId $operandId): array
    {
        $ids = $this->registry->connection(Car::class)
            ->fetchAll('
                SELECT DISTINCT o.car_id 
                FROM orders o
                WHERE o.customer_id = :customer
                GROUP BY o.car_id
            ',
                [
                    'customer' => $operandId,
                ]);

        return $this->registry->viewListBy(Car::class, ['uuid' => array_map('array_shift', $ids)]);
    }

    public function customersByCar(CarId $carId): array
    {
        $ids = $this->registry->connection(Car::class)
            ->fetchAll('
                SELECT DISTINCT o.customer_id 
                FROM orders o
                WHERE o.car_id = :car
                GROUP BY o.customer_id
            ',
                [
                    'car' => $carId,
                ]);

        return $this->registry->viewListBy(Operand::class, ['uuid' => array_map('array_shift', $ids)]);
    }
}
