<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * KeysPrice.
 *
 * @ORM\Table(name="keys_price")
 * @ORM\Entity
 */
class KeysPrice
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
     * @var string
     *
     * @ORM\Column(name="query", type="string", length=255, nullable=true)
     */
    private $query;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=255, nullable=true)
     */
    private $source;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last", type="datetime", nullable=true)
     */
    private $last;

    /**
     * @var bool
     *
     * @ORM\Column(name="cnt", type="boolean", nullable=true)
     */
    private $cnt;

    /**
     * @var string
     *
     * @ORM\Column(name="man", type="string", length=255, nullable=true)
     */
    private $man;
}
