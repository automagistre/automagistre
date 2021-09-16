<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Tenant\Entity\TenantEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class OrderSuspend extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    private UuidInterface $id;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="suspends")
     */
    private $order;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private $till;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $reason;

    public function __construct(Order $order, DateTimeImmutable $till, string $reason)
    {
        $this->id = Uuid::uuid6();
        $this->order = $order;
        $this->till = $till;
        $this->reason = $reason;
    }

    public function toId(): UuidInterface
    {
        return $this->id;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getTill(): DateTimeImmutable
    {
        return $this->till;
    }
}
