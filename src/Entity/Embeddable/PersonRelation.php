<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Customer\Entity\Person;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 *
 * @method Person      entity()
 * @method Person|null entityOrNull()
 */
final class PersonRelation extends Relation
{
    /**
     * @var int|null
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $id;

    public function __construct(Person $entity = null)
    {
        parent::__construct($entity);

        if (null !== $entity) {
            $this->id = $entity->getId();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function entityClass(): string
    {
        return Person::class;
    }
}
