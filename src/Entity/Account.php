<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Enum\AccountType;
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
     * @var AccountType
     *
     * @ORM\Column(type="account_type_enum")
     */
    private $type;

    public function __construct(Operand $owner, string $name, AccountType $type)
    {
        $this->owner = $owner;
        $this->name = $name;
        $this->type = $type;
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
        return $this->type;
    }
}
