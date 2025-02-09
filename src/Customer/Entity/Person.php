<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Tenant\Entity\TenantGroupEntity;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;
use function sprintf;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"telephone", "tenant_group_id"})
 *     }
 * )
 */
class Person extends TenantGroupEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public OperandId $id;

    /**
     * @ORM\Column(length=32, nullable=true)
     */
    public ?string $firstname = null;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $lastname = null;

    /**
     * @ORM\Column(type="phone_number", nullable=true)
     */
    public ?PhoneNumber $telephone = null;

    /**
     * @ORM\Column(type="phone_number", nullable=true)
     */
    public ?PhoneNumber $officePhone = null;

    /**
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
