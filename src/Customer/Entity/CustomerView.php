<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Money\Money;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="customer_view")
 *
 * @psalm-suppress MissingConstructor
 */
class CustomerView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="operand_id")
     */
    public OperandId $id;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $name;

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
}
