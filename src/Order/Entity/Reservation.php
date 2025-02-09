<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Part\Event\PartReserved;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 */
class Reservation extends TenantEntity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column
     */
    private ReservationId $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @var OrderItemPart
     *
     * @ORM\ManyToOne(targetEntity=OrderItemPart::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orderItemPart;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(OrderItemPart $orderItemPart, int $quantity)
    {
        $this->id = ReservationId::generate();
        $this->orderItemPart = $orderItemPart;
        $this->quantity = $quantity;

        if ($quantity > 0) {
            $this->record(new PartReserved($this->id));
        }
    }

    public function toId(): ReservationId
    {
        return $this->id;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getOrderItemPart(): OrderItemPart
    {
        return $this->orderItemPart;
    }
}
