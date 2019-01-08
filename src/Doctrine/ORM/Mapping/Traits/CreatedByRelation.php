<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping\Traits;

use App\Entity\Embeddable\UserRelation;
use App\Entity\Landlord\User;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait CreatedByRelation
{
    /**
     * @var UserRelation
     *
     * @Assert\Valid
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\UserRelation")
     */
    protected $createdBy;

    public function setCreatedBy(User $user): void
    {
        if (null !== $this->createdBy && !$this->createdBy->isEmpty()) {
            throw new LogicException('CreatedBy already defined');
        }

        $this->createdBy = new UserRelation($user);
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy->entity();
    }
}
