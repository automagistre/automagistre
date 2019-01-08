<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Entity\Landlord\Part;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 *
 * @method Part entity()
 */
final class PartRelation extends Relation
{
    /**
     * @var UuidInterface|null
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="uuid_binary", nullable=true)
     */
    protected $uuid;

    public function __construct(Part $entity = null)
    {
        $this->uuid = null !== $entity ? $entity->uuid() : null;

        parent::__construct($entity);
    }

    public static function entityClass(): string
    {
        return Part::class;
    }
}
