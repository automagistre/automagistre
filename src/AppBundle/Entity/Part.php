<?php

namespace AppBundle\Entity;

use AppBundle\Doctrine\PropertyAccessorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Part
 *
 * @ORM\Table(name="part", uniqueConstraints={@ORM\UniqueConstraint(name="part_uniq", columns={"partnumber", "manufacturer_id"})})
 * @ORM\Entity
 */
class Part
{
    use PropertyAccessorTrait;

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
     * @var integer
     *
     * @ORM\Column(name="manufacturer_id", type="integer", nullable=true)
     */
    private $manufacturerId;

    /**
     * @var string
     *
     * @ORM\Column(name="partname", type="string", length=255, nullable=true)
     */
    private $partname;

    /**
     * @var string
     *
     * @ORM\Column(name="partnumber_disp", type="string", length=64, nullable=true)
     */
    private $partnumberDisp;

    /**
     * @var string
     *
     * @ORM\Column(name="partnumber", type="string", length=30, nullable=true)
     */
    private $partnumber;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="negative", type="boolean", nullable=true)
     */
    private $negative;

    /**
     * @var boolean
     *
     * @ORM\Column(name="fractional", type="boolean", nullable=true)
     */
    private $fractional;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="string", length=255, nullable=true)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="float", precision=10, scale=0, nullable=true)
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="reserved", type="integer", nullable=false)
     */
    private $reserved = '0';

}

