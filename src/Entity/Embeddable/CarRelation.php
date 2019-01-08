<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Entity\Landlord\Car;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Embeddable
 *
 * @method Car entity()
 */
final class CarRelation extends Relation
{
    /**
     * @var UuidInterface|null
     *
     * @ORM\Column(type="uuid_binary")
     */
    protected $uuid;

    public function __construct(Car $entity = null)
    {
        $this->uuid = null !== $entity ? $entity->uuid() : null;

        parent::__construct($entity);
    }

    public static function entityClass(): string
    {
        return Car::class;
    }
}
