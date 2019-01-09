<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Entity\Tenant\OrderItemService;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 *
 * @method OrderItemService entity()
 * @method OrderItemService|null entityOrNull()
 */
final class OrderItemServiceRelation extends Relation
{
    /**
     * @var UuidInterface|null
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="uuid_binary", nullable=true)
     */
    protected $uuid;

    public function __construct(OrderItemService $entity = null)
    {
        parent::__construct($entity);
    }

    public static function entityClass(): string
    {
        return OrderItemService::class;
    }
}
