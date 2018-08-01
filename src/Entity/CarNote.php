<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class CarNote extends Note
{
    /**
     * @var Car
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Car")
     */
    public $car;

    public function __construct(Car $car, User $user)
    {
        parent::__construct($user);

        $this->car = $car;
    }

    public function getCar(): Car
    {
        return $this->car;
    }
}
