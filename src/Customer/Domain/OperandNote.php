<?php

declare(strict_types=1);

namespace App\Customer\Domain;

use App\Entity\Superclass\Note;
use App\Shared\Enum\NoteType;
use App\User\Domain\UserId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class OperandNote extends Note
{
    /**
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity=Operand::class)
     */
    public ?Operand $operand = null;

    /**
     * @ORM\Column(type="user_id")
     */
    public UserId $createdBy;

    public function __construct(Operand $operand, UserId $userId, NoteType $noteType = null, string $text = null)
    {
        parent::__construct($noteType, $text);

        $this->operand = $operand;
        $this->createdBy = $userId;
    }
}
