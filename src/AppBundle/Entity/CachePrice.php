<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CachePrice.
 *
 * @ORM\Table(name="cache_price")
 * @ORM\Entity
 */
class CachePrice
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
     * @var float
     *
     * @ORM\Column(name="price", type="float", precision=10, scale=0, nullable=true)
     */
    private $price;

    /**
     * @var bool
     *
     * @ORM\Column(name="term", type="boolean", nullable=true)
     */
    private $term;

    /**
     * @var string
     *
     * @ORM\Column(name="man_name", type="string", length=255, nullable=true)
     */
    private $manName;

    /**
     * @var string
     *
     * @ORM\Column(name="pn", type="string", length=255, nullable=true)
     */
    private $pn;

    /**
     * @var string
     *
     * @ORM\Column(name="part_name", type="text", length=65535, nullable=true)
     */
    private $partName;

    /**
     * @var int
     *
     * @ORM\Column(name="id_price", type="integer", nullable=true)
     */
    private $idPrice;

    /**
     * @var int
     *
     * @ORM\Column(name="id_d2m", type="integer", nullable=true)
     */
    private $idD2m;

    /**
     * @var int
     *
     * @ORM\Column(name="qty", type="integer", nullable=true)
     */
    private $qty;

    /**
     * @var bool
     *
     * @ORM\Column(name="prc_ok", type="boolean", nullable=true)
     */
    private $prcOk;

    /**
     * @var string
     *
     * @ORM\Column(name="dir_name", type="string", length=255, nullable=true)
     */
    private $dirName;

    /**
     * @var int
     *
     * @ORM\Column(name="min_qty", type="integer", nullable=true)
     */
    private $minQty;

    /**
     * @var bool
     *
     * @ORM\Column(name="type_cross", type="boolean", nullable=true)
     */
    private $typeCross;

    /**
     * @var int
     *
     * @ORM\Column(name="qid", type="integer", nullable=true)
     */
    private $qid;

    /**
     * @var bool
     *
     * @ORM\Column(name="grp_part", type="boolean", nullable=true)
     */
    private $grpPart;
}
