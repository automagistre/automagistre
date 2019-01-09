<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping\Traits;

use App\Entity\Landlord\User;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait CreatedBy
{
    /**
     * @var User
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    public function setCreatedBy(User $user): void
    {
        if (null !== $this->createdBy) {
            throw new LogicException('CreatedBy already defined');
        }

        $this->createdBy = $user;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }
}
