<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Portlet.
 *
 * @ORM\Table(name="portlet")
 * @ORM\Entity
 */
class Portlet
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
     * @ORM\Column(name="_user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="layoutid", type="string", length=100, nullable=true)
     */
    private $layoutid;

    /**
     * @var string
     *
     * @ORM\Column(name="viewtype", type="text", length=65535, nullable=true)
     */
    private $viewtype;

    /**
     * @var string
     *
     * @ORM\Column(name="serializedviewdata", type="text", length=65535, nullable=true)
     */
    private $serializedviewdata;

    /**
     * @var bool
     *
     * @ORM\Column(name="collapsed", type="boolean", nullable=true)
     */
    private $collapsed;

    /**
     * @var array
     *
     * @ORM\Column(name="column", type="simple_array", nullable=true)
     */
    private $column;

    /**
     * @var bool
     *
     * @ORM\Column(name="position", type="boolean", nullable=true)
     */
    private $position;
}
