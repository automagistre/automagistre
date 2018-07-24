<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Note
{
    use Identity;
    use CreatedAt;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="notes")
     * @ORM\JoinColumn
     */
    private $order;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    public function __toString(): string
    {
        return $this->getDescription();
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
