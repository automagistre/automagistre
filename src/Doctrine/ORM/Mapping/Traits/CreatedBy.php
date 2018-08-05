<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping\Traits;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait CreatedBy
{
    /**
     * @var User
     *
     * @Gedmo\Blameable(on="create")
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $createdBy;

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }
}
