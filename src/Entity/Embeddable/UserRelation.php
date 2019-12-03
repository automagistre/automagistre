<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank
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
