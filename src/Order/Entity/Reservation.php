<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Part\Event\PartReserved;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Reservation implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column(type="reservation_id")
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
