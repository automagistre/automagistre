<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emailmessagesender
 *
 * @ORM\Table(name="emailmessagesender")
 * @ORM\Entity
 */
class Emailmessagesender
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
     * @ORM\Column(name="fromname", type="string", length=64, nullable=true)
     */
    private $fromname;

    /**
     * @var string
     *
     * @ORM\Column(name="fromaddress", type="string", length=255, nullable=true)
     */
    private $fromaddress;

}

