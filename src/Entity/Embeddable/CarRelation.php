<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Car\Entity\Car;
use Doctrine\ORM\Mapping as ORM;
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
     * @var int|null
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $id;

    public function __construct(Car $entity = null)
    {
        parent::__construct($entity);

        if (null !== $entity) {
            $this->id = $entity->getId();
        }
    }

    public static function entityClass(): string
    {
        return Car::class;
    }
}
