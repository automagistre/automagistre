<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Email
 *
 * @ORM\Table(name="email", uniqueConstraints={@ORM\UniqueConstraint(name="UQ_b01d23692bac458cf1cd67ed9aaefd9d97f3e45f", columns={"emailaddress"})})
 * @ORM\Entity
 */
class Email
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isinvalid", type="boolean", nullable=true)
     */
    private $isinvalid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="optout", type="boolean", nullable=true)
     */
    private $optout;

    /**
     * @var string
     *
     * @ORM\Column(name="emailaddress", type="string", length=255, nullable=true)
     */
    private $emailaddress;

}

