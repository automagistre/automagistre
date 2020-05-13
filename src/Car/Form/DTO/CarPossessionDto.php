<?php

declare(strict_types=1);

namespace App\Car\Form\DTO;

use App\Car\Entity\CarId;
use App\Customer\Domain\OperandId;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-immutable
 */
final class CarPossessionDto
{
    /**
     * @var CarId
     *
     * @Assert\NotBlank
     */
    public $carId;

    /**
     * @var OperandId
     *
     * @Assert\NotBlank
     */
    public $possessorId;

    public function __construct(CarId $carId = null, OperandId $possessorId = null)
    {
        $this->carId = $carId;
        $this->possessorId = $possessorId;
    }
}
