<?php

namespace AppBundle\Entity;

use AppBundle\Doctrine\PropertyAccessorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Motion
{
    use PropertyAccessorTrait;

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
     * @ORM\Column(name="qty", type="integer", nullable=true)
     */
    private $qty;

    /**
     * @var int
     *
     * @ORM\Column(name="reserve", type="integer", nullable=true)
     */
    private $reserve;

    /**
     * @var PartItem
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PartItem")
     * @ORM\JoinColumn()
     */
    private $partItem;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;
}
