<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\AccountType;
use App\Entity\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Account
{
    use Identity;

    /**
     * @var Operand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand", inversedBy="accounts")
     */
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $type;

    public function __construct(Operand $owner, string $name, AccountType $type)
    {
        $this->owner = $owner;
        $this->name = $name;
        $this->type = $type->getId();
    }

    public function getOwner(): Operand
    {
        return $this->owner;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): AccountType
    {
        return new AccountType($this->type);
    }
}
