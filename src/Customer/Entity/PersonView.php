<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Tenant\Entity\TenantGroupEntity;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Money\Money;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="person_view")
 *
 * @psalm-suppress MissingConstructor
 */
class PersonView extends TenantGroupEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="operand_id")
     */
    public OperandId $id;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $fullName;

    /**
     * @ORM\Column(type="money")
     */
    public Money $balance;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $email = null;

    /**
     * @ORM\Column(type="phone_number", nullable=true)
     */
    public ?PhoneNumber $telephone = null;

    /**
     * @ORM\Column(type="phone_number", nullable=true)
     */
    public ?PhoneNumber $officePhone = null;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $contractor = false;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $seller = false;

    public function toId(): OperandId
    {
        return $this->id;
    }
}
