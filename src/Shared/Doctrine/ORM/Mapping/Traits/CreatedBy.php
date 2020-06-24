<?php

declare(strict_types=1);

namespace App\Shared\Doctrine\ORM\Mapping\Traits;

use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use LogicException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait CreatedBy
{
    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $createdBy = null;

    public function getCreatedBy(): User
    {
        if (null === $this->createdBy) {
            throw new LogicException('CreatedBy must be set first.');
        }

        return $this->createdBy;
    }
}
