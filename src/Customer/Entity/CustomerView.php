<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Tenant\Entity\TenantGroupEntity;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use LogicException;
use Money\Money;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="customer_view")
 *
 * @psalm-suppress MissingConstructor
 */
class CustomerView extends TenantGroupEntity
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

    /**
     * @ORM\Column
     */
    public string $type;

    public function toId(): OperandId
    {
        return $this->id;
    }

    public function toClass(): string
    {
        return match ($this->type) {
            'organization' => Organization::class,
            'person' => Person::class,
            default => throw new LogicException('Unexpected type.'),
        };
    }
}
