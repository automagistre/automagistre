<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pointtransaction
 *
 * @ORM\Table(name="pointtransaction")
 * @ORM\Entity
 */
class Pointtransaction
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
     * @ORM\Column(name="item_id", type="integer", nullable=true)
     */
    private $itemId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createddatetime", type="datetime", nullable=true)
     */
    private $createddatetime;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float", precision=10, scale=0, nullable=true)
     */
    private $value;

    /**
     * @var integer
     *
     * @ORM\Column(name="point_id", type="integer", nullable=true)
     */
    private $pointId;

}

