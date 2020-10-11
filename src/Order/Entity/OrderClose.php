<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Order\Messages\OrderClosed;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 */
class OrderClose implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    /**
     * @ORM\OneToOne(targetEntity=Order::class, inversedBy="close")
     */
    public Order $order;

    /**
     * @ORM\Column(type="money", nullable=true)
     */
    public ?Money $balance = null;

    public function __construct(Order $order, ?Money $balance)
    {
        $this->id = Uuid::uuid6();
        $this->order = $order;
        $this->balance = $balance;

        $this->record(new OrderClosed($order->toId()));
    }

    public function toId(): UuidInterface
    {
        return $this->id;
    }
}
