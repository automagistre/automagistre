<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Entity\Landlord\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 *
 * @method User entity()
 * @method User|null entityOrNull()
 */
final class UserRelation extends Relation
{
    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $id;

    public function __construct(User $entity = null)
    {
        parent::__construct($entity);

        if (null !== $entity) {
            $this->id = $entity->getId();
        }
    }

    public static function entityClass(): string
    {
        return User::class;
    }
}
