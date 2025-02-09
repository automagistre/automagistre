<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use App\Keycloak\Entity\UserId;
use DateTimeImmutable;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="appeal_postpone")
 *
 * @psalm-immutable
 */
class Postpone extends TenantEntity
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
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(UuidInterface $id, AppealId $appealId)
    {
        $this->id = $id;
        $this->appealId = $appealId;
    }

    public static function create(AppealId $appealId): self
    {
        return new self(
            Uuid::uuid6(),
            $appealId,
        );
    }
}
