<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\CreatedBy;
use App\Entity\Superclass\Note;
use App\Enum\NoteType;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class OperandNote extends Note
{
    use CreatedBy;

    /**
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\Operand")
     */
    public ?Operand $operand = null;

    public function __construct(Operand $operand, User $user, NoteType $noteType = null, string $text = null)
    {
        parent::__construct($noteType, $text);

        $this->operand = $operand;
        $this->createdBy = $user;
    }
}
