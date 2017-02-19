<?php

namespace AppBundle\Entity;

use AppBundle\Doctrine\PropertyAccessorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Manufacturer
 *
 * @ORM\Table(name="manufacturer", uniqueConstraints={@ORM\UniqueConstraint(name="UQ_c17cf66b38ac3bd928a6ebf320320881ce022754", columns={"logoem"}), @ORM\UniqueConstraint(name="UQ_e866d1f7bc3130384a2fd1ad1ddd50921a0101b9", columns={"logopl"})})
 * @ORM\Entity
 */
class Manufacturer
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=25, nullable=true)
     */
    private $logo;

    /**
     * @var string
     *
     * @ORM\Column(name="logoad", type="string", length=25, nullable=true)
     */
    private $logoad;

    /**
     * @var string
     *
     * @ORM\Column(name="logoem", type="string", length=25, nullable=true)
     */
    private $logoem;

    /**
     * @var boolean
     *
     * @ORM\Column(name="bitoriginal", type="boolean", nullable=true)
     */
    private $bitoriginal;

    /**
     * @var string
     *
     * @ORM\Column(name="logopl", type="string", length=25, nullable=true)
     */
    private $logopl;

}

