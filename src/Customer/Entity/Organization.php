<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Tenant\Entity\TenantGroupEntity;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhone;
use Symfony\Component\Validator\Constraints as Assert;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 */
class Organization extends TenantGroupEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public OperandId $id;

    /**
     * @var Requisite
     *
     * @Assert\Valid
     *
     * @ORM\Embedded(class=Requisite::class)
     */
    public $requisite;

    /**
     * @var string
     *
     * @ORM\Column
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    public $address;

    /**
     * @AssertPhone
     *
     * @ORM\Column(type="phone_number", nullable=true)
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

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(OperandId $id)
    {
        $this->id = $id;
        $this->requisite = new Requisite();
    }

    public function toId(): OperandId
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
