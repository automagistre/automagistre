<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Keycloak\Entity\UserId;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Tenant\Entity\TenantEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "1": "App\Order\Entity\OrderDeal",
 *     "2": "App\Order\Entity\OrderCancel",
 * })
 */
abstract class OrderClose extends TenantEntity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column
     */
    public UuidInterface $id;

    /**
     * @ORM\OneToOne(targetEntity=Order::class, inversedBy="close")
     */
    public Order $order;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(Order $order)
    {
        $this->id = Uuid::uuid6();
        $this->order = $order;
    }

    public function toId(): UuidInterface
    {
        return $this->id;
    }
}
