<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Order\Entity\OrderItemService;
use App\Tenant\Tenant;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 *
 * @method OrderItemService      entity()
 * @method OrderItemService|null entityOrNull()
 */
final class OrderItemServiceRelation extends Relation
{
    /**
     * @var int|null
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $id;

    /**
     * @var Tenant|null
     *
     * @ORM\Column(type="tenant_enum", nullable=true)
     */
    protected $tenant;

    public function __construct(OrderItemService $entity = null, Tenant $tenant = null)
    {
        parent::__construct($entity);

        if (null !== $entity) {
            $this->id = $entity->getId();
        }

        $this->tenant = $tenant;
    }

    public static function entityClass(): string
    {
        return OrderItemService::class;
    }
}
