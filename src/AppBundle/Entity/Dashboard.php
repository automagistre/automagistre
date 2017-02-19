<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dashboard
 *
 * @ORM\Table(name="dashboard")
 * @ORM\Entity
 */
class Dashboard
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
     * @ORM\Column(name="ownedsecurableitem_id", type="integer", nullable=true)
     */
    private $ownedsecurableitemId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isdefault", type="boolean", nullable=true)
     */
    private $isdefault;

    /**
     * @var string
     *
     * @ORM\Column(name="layouttype", type="string", length=10, nullable=true)
     */
    private $layouttype;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="layoutid", type="integer", nullable=true)
     */
    private $layoutid;

}

