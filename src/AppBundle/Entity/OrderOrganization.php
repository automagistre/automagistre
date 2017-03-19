<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class OrderOrganization extends Order
{
    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organization")
     */
    protected $customer;
}
