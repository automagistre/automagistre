<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
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
abstract class OrderClose implements ContainsRecordedMessages
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
