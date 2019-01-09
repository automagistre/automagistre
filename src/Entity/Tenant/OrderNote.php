<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\CreatedByRelation as CreatedBy;
use App\Entity\Landlord\User;
use App\Entity\Superclass\Note;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class OrderNote extends Note
{
    use CreatedBy;

    /**
     * @var Order
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenant\Order")
     */
    private $order;

    public function __construct(Order $order, User $user)
    {
        $this->order = $order;
        $this->setCreatedBy($user);
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
