<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailmessageRead
 *
 * @ORM\Table(name="emailmessage_read")
 * @ORM\Entity
 */
class EmailmessageRead
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
     * @ORM\Column(name="munge_id", type="integer", nullable=true)
     */
    private $mungeId;

}

