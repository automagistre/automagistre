<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class OrderNote extends Note
{
    /**
     * @var Order
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Order")
     */
    private $order;

    public function __construct(Order $order, User $user)
    {
        parent::__construct($user);

        $this->order = $order;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
