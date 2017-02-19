<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Joblog.
 *
 * @ORM\Table(name="joblog")
 * @ORM\Entity
 */
class Joblog
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
     * @ORM\Column(name="item_id", type="integer", nullable=true)
     */
    private $itemId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="enddatetime", type="datetime", nullable=true)
     */
    private $enddatetime;

    /**
     * @var bool
     *
     * @ORM\Column(name="isprocessed", type="boolean", nullable=true)
     */
    private $isprocessed;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=true)
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startdatetime", type="datetime", nullable=true)
     */
    private $startdatetime;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=64, nullable=true)
     */
    private $type;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;
}
