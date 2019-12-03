<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping\Traits;

use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\ManyToOne(targetEntity="App\User\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }
}
