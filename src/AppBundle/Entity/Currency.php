<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Currency.
 *
 * @ORM\Table(name="currency", uniqueConstraints={@ORM\UniqueConstraint(name="UQ_4f5a32b86618fd9d6a870ffe890cf77a88669783", columns={"code"})})
 * @ORM\Entity
 */
class Currency
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
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var array
     *
     * @ORM\Column(name="ratetobase", type="simple_array", nullable=true)
     */
    private $ratetobase;
}
