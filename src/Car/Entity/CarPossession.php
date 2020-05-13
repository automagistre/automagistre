<?php

declare(strict_types=1);

namespace App\Car\Entity;

use App\Customer\Domain\OperandId;
use App\Shared\Enum\Transition;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class CarPossession
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="operand_id")
     */
    private OperandId $possessorId;

    /**
     * @ORM\Column(type="car_id")
     */
    private CarId $carId;

    /**
     * @ORM\Column(type="transition_enum")
     */
    private Transition $transition;

    public function __construct(OperandId $possessorId, CarId $carId, Transition $transition)
    {
        $this->id = Uuid::uuid6();
        $this->possessorId = $possessorId;
        $this->carId = $carId;
        $this->transition = $transition;
    }
}
