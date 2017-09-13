<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class PartnerOperand
{
    /**
     * @var Operand
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="App\Entity\Operand")
     */
    private $operand;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $name;

    public function __construct(string $name, Operand $operand)
    {
        $this->name = $name;
        $this->operand = $operand;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOperand(): Operand
    {
        return $this->operand;
    }
}
