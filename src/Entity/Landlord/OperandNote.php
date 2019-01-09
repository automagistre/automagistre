<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\CreatedBy;
use App\Entity\Superclass\Note;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class OperandNote extends Note
{
    use CreatedBy;

    /**
     * @var Operand
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\Operand")
     */
    private $operand;

    public function __construct(Operand $operand, User $user)
    {
        $this->operand = $operand;
        $this->setCreatedBy($user);
    }

    public function getOperand(): Operand
    {
        return $this->operand;
    }
}
