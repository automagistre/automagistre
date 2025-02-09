<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use App\Appeal\Enum\AppealStatus;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 * @ORM\Table(name="appeal_status")
 *
 * @psalm-immutable
 */
class Status extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public UuidInterface $id;

    /**
     * @ORM\Column
     */
    public AppealId $appealId;

    /**
     * @ORM\Column(type="appeal_status")
     */
    public AppealStatus $status;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(UuidInterface $id, AppealId $appealId, AppealStatus $status)
    {
        $this->id = $id;
        $this->appealId = $appealId;
        $this->status = $status;
    }

    public static function create(AppealId $appealId, AppealStatus $status): self
    {
        return new self(
            Uuid::uuid6(),
            $appealId,
            $status,
        );
    }
}
