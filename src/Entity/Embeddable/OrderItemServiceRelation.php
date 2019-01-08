<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Entity\Tenant\OrderItemService;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Embeddable
 *
 * @method OrderItemService entity()
 */
final class OrderItemServiceRelation extends Relation
{
    /**
     * @var UuidInterface|null
     *
     * @ORM\Column(type="uuid_binary")
     */
    protected $uuid;

    public function __construct(OrderItemService $entity = null)
    {
        $this->uuid = null !== $entity ? $entity->uuid() : null;

        parent::__construct($entity);
    }

    public static function entityClass(): string
    {
        return OrderItemService::class;
    }
}
