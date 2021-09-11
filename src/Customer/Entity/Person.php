<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Tenant\Entity\TenantGroupEntity;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhone;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use function sprintf;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"telephone", "tenant_group_id"})
 *     }
 * )
 *
 * @UniqueEntity(fields={"telephone", "tenantGroupId"}, message="Заказчик с таким телефоном уже существует")
 */
class Person extends TenantGroupEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="operand_id")
     */
    public OperandId $id;

    /**
     * @Assert\Length(max="32")
     *
     * @ORM\Column(length=32, nullable=true)
     */
    public ?string $firstname = null;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $lastname = null;

    /**
     * @AssertPhone
     *
     * @ORM\Column(type="phone_number", nullable=true, unique=true)
     */
    public ?PhoneNumber $telephone = null;

    /**
     * @AssertPhone
     *
     * @ORM\Column(type="phone_number", nullable=true)
     */
    public ?PhoneNumber $officePhone = null;

    /**
     * @Assert\Email
     *
     * @ORM\Column(nullable=true)
     */
    public ?string $email = null;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $contractor = false;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $seller = false;

    public function __construct(OperandId $id)
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        $string = $this->getFullName();
        $email = $this->email;

        if (null !== $email) {
            $string .= sprintf(' (%s)', $email);
        }

        return $string;
    }

    public function toId(): OperandId
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->lastname, $this->firstname);
    }
}
