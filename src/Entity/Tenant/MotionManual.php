<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Entity\Embeddable\UserRelation;
use App\Entity\Landlord\Part;
use App\Entity\Landlord\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MotionManual extends Motion
{
    /**
     * @var UserRelation
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\UserRelation")
     */
    private $user;

    public function __construct(User $user, Part $part, int $quantity, string $description)
    {
        parent::__construct($part, $quantity, $description);

        $this->user = new UserRelation($user);
    }

    public function getUser(): User
    {
        return $this->user->entity();
    }
}
