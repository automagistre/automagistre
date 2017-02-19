<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Partcart.
 *
 * @ORM\Table(name="partcart")
 * @ORM\Entity
 */
class Partcart
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
     * @var int
     *
     * @ORM\Column(name="qty", type="smallint", nullable=true)
     */
    private $qty;

    /**
     * @var int
     *
     * @ORM\Column(name="_order_id", type="integer", nullable=true)
     */
    private $orderId;

    /**
     * @var bool
     *
     * @ORM\Column(name="qty_stock", type="boolean", nullable=true)
     */
    private $qtyStock;
}
