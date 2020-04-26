<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Part\Domain\Part;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 *
 * @method Part entity()
 * @method Part|null entityOrNull()
 */
final class PartRelation extends Relation
{
    /**
     * @var int|null
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $id;

    public function __construct(Part $entity = null)
    {
        parent::__construct($entity);

        if (null !== $entity) {
            $this->id = $entity->getId();
        }
    }

    public static function entityClass(): string
    {
        return Part::class;
    }
}
