<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class OperandNote extends Note
{
    /**
     * @var Operand
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand")
     */
    private $operand;

    public function __construct(Operand $operand, User $user)
    {
        parent::__construct($user);

        $this->operand = $operand;
    }

    public function getOperand(): Operand
    {
        return $this->operand;
    }
}
