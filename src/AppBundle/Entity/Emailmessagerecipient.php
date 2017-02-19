<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emailmessagerecipient
 *
 * @ORM\Table(name="emailmessagerecipient")
 * @ORM\Entity
 */
class Emailmessagerecipient
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
     * @var integer
     *
     * @ORM\Column(name="personoraccount_item_id", type="integer", nullable=true)
     */
    private $personoraccountItemId;

    /**
     * @var string
     *
     * @ORM\Column(name="toname", type="string", length=64, nullable=true)
     */
    private $toname;

    /**
     * @var string
     *
     * @ORM\Column(name="toaddress", type="string", length=255, nullable=true)
     */
    private $toaddress;

    /**
     * @var array
     *
     * @ORM\Column(name="type", type="simple_array", nullable=true)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="emailmessage_id", type="integer", nullable=true)
     */
    private $emailmessageId;

}

