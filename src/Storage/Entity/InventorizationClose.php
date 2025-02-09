<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Storage\Event\InventorizationClosed;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 *
 * @psalm-immutable
 */
class InventorizationClose extends TenantEntity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column
     */
    private UuidInterface $id;

    /**
     * @ORM\OneToOne(targetEntity=Inventorization::class, inversedBy="close")
     */
    private Inventorization $inventorization;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(Inventorization $inventorization)
    {
        $this->id = Uuid::uuid6();
        $this->inventorization = $inventorization;

        $this->record(new InventorizationClosed($inventorization->toId()));
    }
}
