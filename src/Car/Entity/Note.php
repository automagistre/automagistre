<?php

declare(strict_types=1);

namespace App\Car\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedBy;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Enum\NoteType;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="car_note")
 */
class Note
{
    use Identity;
    use CreatedBy;
    use CreatedAt;

    /**
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Car\Entity\Car")
     */
    public Car $car;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(type="note_type_enum", nullable=false)
     */
    public ?NoteType $type = null;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="text")
     */
    public ?string $text = null;

    public function __construct(Car $car, User $user, NoteType $type = null, string $text = null)
    {
        $this->car = $car;
        $this->createdBy = $user;
        $this->type = $type;
        $this->text = $text;
    }
}
