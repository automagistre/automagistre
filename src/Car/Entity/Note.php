<?php

declare(strict_types=1);

namespace App\Car\Entity;

use App\Shared\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use App\Shared\Enum\NoteType;
use App\User\Domain\UserId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="car_note")
 */
class Note
{
    use Identity;
    use CreatedAt;

    /**
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity=Car::class)
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

    /**
     * @ORM\Column(type="user_id")
     */
    public UserId $createdBy;

    public function __construct(Car $car, UserId $userId, NoteType $type = null, string $text = null)
    {
        $this->car = $car;
        $this->createdBy = $userId;
        $this->type = $type;
        $this->text = $text;
    }
}
