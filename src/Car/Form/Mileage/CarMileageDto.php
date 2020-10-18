<?php

declare(strict_types=1);

namespace App\Car\Form\Mileage;

use App\Car\Entity\CarId;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class CarMileageDto
{
    public CarId $carId;

    /**
     * @var int
     *
     * @Assert\NotBlank
     */
    public $mileage;

    public function __construct(CarId $carId)
    {
        $this->carId = $carId;
    }
}
