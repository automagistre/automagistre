<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait Identity
{
    /**
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
