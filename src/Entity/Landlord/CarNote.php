<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\CreatedBy;
use App\Entity\Superclass\Note;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class CarNote extends Note
{
    use CreatedBy;

    /**
     * @var Car
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\Car")
     */
    public $car;

    public function __construct(Car $car, User $user)
    {
        $this->car = $car;
        $this->createdBy = $user;
    }

    public function getCar(): Car
    {
        return $this->car;
    }
}
