<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Auditevent.
 *
 * @ORM\Table(name="auditevent")
 * @ORM\Entity
 */
class Auditevent
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="modelid", type="integer", nullable=true)
     */
    private $modelid;

    /**
     * @var int
     *
     * @ORM\Column(name="_user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime", nullable=true)
     */
    private $datetime;

    /**
     * @var string
     *
     * @ORM\Column(name="eventname", type="string", length=64, nullable=true)
     */
    private $eventname;

    /**
     * @var string
     *
     * @ORM\Column(name="modulename", type="string", length=64, nullable=true)
     */
    private $modulename;

    /**
     * @var string
     *
     * @ORM\Column(name="modelclassname", type="string", length=64, nullable=true)
     */
    private $modelclassname;

    /**
     * @var string
     *
     * @ORM\Column(name="serializeddata", type="text", length=65535, nullable=true)
     */
    private $serializeddata;
}
