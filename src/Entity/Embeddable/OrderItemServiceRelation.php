<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Entity\Tenant\OrderItemService;
use App\Enum\Tenant;
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

    /**
     * @var Tenant|null
     *
     * @ORM\Column(type="tenant_enum", nullable=true)
     */
    protected $tenant;

    public function __construct(OrderItemService $entity = null, Tenant $tenant = null)
    {
        parent::__construct($entity);

        $this->tenant = $tenant;
    }

    public static function entityClass(): string
    {
        return OrderItemService::class;
    }
}
