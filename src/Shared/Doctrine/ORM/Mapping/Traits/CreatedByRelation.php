<?php

declare(strict_types=1);

namespace App\Shared\Doctrine\ORM\Mapping\Traits;

use App\Entity\Embeddable\UserRelation;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait CreatedByRelation
{
    /**
     * @Assert\Valid
     *
     * @ORM\Embedded(class=UserRelation::class, columnPrefix="created_by_")
     */
    protected ?UserRelation $createdByRelation = null;

    public function getCreatedBy(): User
    {
        if (null === $this->createdByRelation) {
            throw new LogicException('CreatedBy must be set first.');
        }

        return $this->createdByRelation->entity();
    }
}
