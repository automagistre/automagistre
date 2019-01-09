<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Entity\Landlord\User;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
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
     * @var UuidInterface|null
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="uuid_binary", nullable=true)
     */
    protected $uuid;

    public function __construct(User $entity = null)
    {
        parent::__construct($entity);
    }

    public static function entityClass(): string
    {
        return User::class;
    }
}
