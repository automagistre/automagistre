<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Integrationpart.
 *
 * @ORM\Table(name="integrationpart")
 * @ORM\Entity
 */
class Integrationpart
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
     * @var int
     *
     * @ORM\Column(name="part_id", type="integer", nullable=true)
     */
    private $partId;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @var int
     *
     * @ORM\Column(name="gid", type="integer", nullable=true)
     */
    private $gid;

    /**
     * @var int
     *
     * @ORM\Column(name="zc", type="integer", nullable=true)
     */
    private $zc;

    /**
     * @var string
     *
     * @ORM\Column(name="supplier", type="string", length=255, nullable=true)
     */
    private $supplier;

    /**
     * @var string
     *
     * @ORM\Column(name="price_in", type="string", length=255, nullable=true)
     */
    private $priceIn;

    /**
     * @var int
     *
     * @ORM\Column(name="qty", type="smallint", nullable=true)
     */
    private $qty;

    /**
     * @var int
     *
     * @ORM\Column(name="state", type="smallint", nullable=true)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="text", length=65535, nullable=true)
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="alt_date", type="string", length=255, nullable=true)
     */
    private $altDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="committed", type="boolean", nullable=true)
     */
    private $committed;

    /**
     * @var bool
     *
     * @ORM\Column(name="error", type="boolean", nullable=true)
     */
    private $error;

    /**
     * @var string
     *
     * @ORM\Column(name="due_date", type="string", length=255, nullable=true)
     */
    private $dueDate;
}
