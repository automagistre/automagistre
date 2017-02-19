<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emailmessagesenderror.
 *
 * @ORM\Table(name="emailmessagesenderror")
 * @ORM\Entity
 */
class Emailmessagesenderror
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
     * @var \DateTime
     *
     * @ORM\Column(name="createddatetime", type="datetime", nullable=true)
     */
    private $createddatetime;

    /**
     * @var string
     *
     * @ORM\Column(name="serializeddata", type="text", nullable=true)
     */
    private $serializeddata;
}
