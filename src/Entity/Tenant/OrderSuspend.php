<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedByRelation as CreatedBy;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Embeddable\UserRelation;
use App\User\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OrderSuspend
{
    use Identity;
    use CreatedAt;
    use CreatedBy;

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

    public function __construct(Order $order, DateTimeImmutable $till, string $reason, User $user)
    {
        $this->order = $order;
        $this->till = $till;
        $this->reason = $reason;
        $this->createdByRelation = new UserRelation($user);
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
