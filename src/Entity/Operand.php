<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="integer")
 * @ORM\DiscriminatorMap({"1": "Person", "2": "Organization"})
 */
abstract class Operand
{
    use Identity;

    /**
     * @var Account[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Account", mappedBy="owner")
     */
    private $accounts;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
    }

    public function addAccount(Account $account): void
    {
        $this->accounts[] = $account;
    }

    public function getAccounts(): array
    {
        return $this->accounts->toArray();
    }

    abstract public function getFullName(): string;

    abstract public function getTelephone(): ?string;
}
