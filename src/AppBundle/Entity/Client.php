<?php

namespace AppBundle\Entity;

use AppBundle\Doctrine\PropertyAccessorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 *
 * @ORM\Table(name="client", indexes={@ORM\Index(name="EID_IDX", columns={"eid"}), @ORM\Index(name="IDX_CLIENT_PERSON", columns={"person_id"})})
 * @ORM\Entity
 */
class Client
{
    use PropertyAccessorTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Person")
     * @ORM\JoinColumn()
     */
    private $person;

    /**
     * @var integer
     *
     * @ORM\Column(name="wallet", type="integer", nullable=false)
     */
    private $wallet = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="employee", type="boolean", nullable=true)
     */
    private $employee;

    /**
     * @var integer
     *
     * @ORM\Column(name="eid", type="integer", nullable=true)
     */
    private $eid;

    /**
     * @var integer
     *
     * @ORM\Column(name="referal_client_id", type="integer", nullable=true)
     */
    private $referalClientId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ref_bonus", type="boolean", nullable=true)
     */
    private $refBonus;

    /**
     * @var integer
     *
     * @ORM\Column(name="point_id", type="integer", nullable=true)
     */
    private $pointId;

}

