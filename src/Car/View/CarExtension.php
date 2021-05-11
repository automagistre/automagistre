<?php

declare(strict_types=1);

namespace App\Car\View;

use App\Car\Entity\Car;
use App\Car\Entity\CarId;
use App\Shared\Doctrine\Registry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CarExtension extends AbstractExtension
{
    public function __construct(private Registry $registry)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'get_car_by_id',
                fn (CarId $carId) => $this->registry->getBy(Car::class, ['id' => $carId]),
            ),
        ];
    }
}
