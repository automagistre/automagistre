<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Payment
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
     * @var Operand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand")
     * @ORM\JoinColumn()
     */
    private $recipient;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(type="float", precision=10, scale=0, nullable=true)
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(type="float", precision=10, scale=0, nullable=true)
     */
    private $subtotal;
}
