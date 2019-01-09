<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Entity\Landlord\Car;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 *
 * @method Car entity()
 * @method Car|null entityOrNull()
 */
final class CarRelation extends Relation
{
    /**
     * @var UuidInterface|null
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="uuid_binary", nullable=true)
     */
    protected $uuid;

    public function __construct(Car $entity = null)
    {
        parent::__construct($entity);
    }

    public static function entityClass(): string
    {
        return Car::class;
    }
}
