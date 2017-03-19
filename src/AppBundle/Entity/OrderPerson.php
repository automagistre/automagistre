<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OrderPerson extends Order
{
    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Person")
     */
    protected $customer;

    public function getCustomer(): ?Person
    {
        return $this->customer;
    }

    public function setCustomer(Person $customer): void
    {
        $this->customer = $customer;
    }
}
