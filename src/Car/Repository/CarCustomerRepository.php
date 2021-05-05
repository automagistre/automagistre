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
        $cars = $this->registry->connection(Car::class)
            ->fetchAllAssociative(
                '
                SELECT DISTINCT o.car_id
                FROM orders o
                WHERE o.customer_id = :customer
                AND o.car_id IS NOT NULL
                GROUP BY o.car_id
            ',
                [
                    'customer' => $operandId,
                ],
            )
        ;

        return $this->registry->viewListBy(Car::class, [
            'id' => array_map(
                static fn (array $car): string => $car['car_id'],
                $cars,
            ),
        ]);
    }

    public function customersByCar(CarId $carId): array
    {
        $customers = $this->registry->connection(Car::class)
            ->fetchAllAssociative(
                '
                SELECT DISTINCT o.customer_id
                FROM orders o
                WHERE o.car_id = :car
                AND o.customer_id IS NOT NULL
                GROUP BY o.customer_id
            ',
                [
                    'car' => $carId,
                ],
            )
        ;

        return $this->registry->viewListBy(Operand::class, [
            'id' => array_map(
                static fn (array $customer): string => $customer['customer_id'],
                $customers,
            ),
        ]);
    }
}
