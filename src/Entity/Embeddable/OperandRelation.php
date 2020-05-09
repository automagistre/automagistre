<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Customer\Domain\Operand;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 *
 * @method Operand      entity()
 * @method Operand|null entityOrNull()
 */
final class OperandRelation extends Relation
{
    /**
     * @var int|null
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $id;

    public function __construct(Operand $entity = null)
    {
        parent::__construct($entity);

        if (null !== $entity) {
            $this->id = $entity->getId();
        }
    }

    public static function entityClass(): string
    {
        return Operand::class;
    }
}
