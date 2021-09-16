<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use App\Appeal\Enum\AppealStatus;
use App\Appeal\Enum\AppealType;
use App\Tenant\Entity\TenantEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;

/**
 * @ORM\Entity
 * @ORM\Table(name="appeal_view")
 *
 * @psalm-suppress MissingConstructor
 */
class AppealView extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public AppealId $id;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Column(type="appeal_type")
     */
    public AppealType $type;

    /**
     * @ORM\Column(type="phone_number", nullable=true)
     */
    public ?PhoneNumber $phone = null;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $email = null;

    /**
     * @ORM\Column(type="appeal_status")
     */
    public AppealStatus $status;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function toId(): AppealId
    {
        return $this->id;
    }
}
