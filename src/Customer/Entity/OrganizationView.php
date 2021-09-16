<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Tenant\Entity\TenantGroupEntity;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Money\Money;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="organization_view")
 *
 * @psalm-suppress MissingConstructor
 */
class OrganizationView extends TenantGroupEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public OperandId $id;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $fullName;

    /**
     * @ORM\Embedded(class=Requisite::class)
     */
    public Requisite $requisite;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    public $address;

    /**
     * @ORM\Column(type="money")
     */
    public Money $balance;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $email = null;

    /**
     * @ORM\Column(type="phone_number")
     */
    public ?PhoneNumber $telephone = null;

    /**
     * @ORM\Column(type="phone_number")
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
