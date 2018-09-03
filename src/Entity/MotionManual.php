<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MotionManual extends Motion
{
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    public function __construct(User $user, Part $part, int $quantity, string $description)
    {
        parent::__construct($part, $quantity, $description);

        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
