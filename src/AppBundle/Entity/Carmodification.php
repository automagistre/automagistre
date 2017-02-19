<?php

namespace AppBundle\Entity;

use AppBundle\Doctrine\PropertyAccessorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Carmodification.
 *
 * @ORM\Table(name="carmodification", indexes={@ORM\Index(name="IDX_MODIF_FOLDER", columns={"folder"}), @ORM\Index(name="IDX_MODIF_PARENT", columns={"cargeneration_id"})})
 * @ORM\Entity
 */
class Carmodification
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
     * @ORM\Column(name="cargeneration_id", type="integer", nullable=true)
     */
    private $cargenerationId;

    /**
     * @var int
     *
     * @ORM\Column(name="folder", type="integer", nullable=true)
     */
    private $folder;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=30, nullable=true)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="hp", type="smallint", nullable=true)
     */
    private $hp;

    /**
     * @var bool
     *
     * @ORM\Column(name="doors", type="boolean", nullable=true)
     */
    private $doors;

    /**
     * @var int
     *
     * @ORM\Column(name="from", type="smallint", nullable=true)
     */
    private $from;

    /**
     * @var int
     *
     * @ORM\Column(name="till", type="smallint", nullable=true)
     */
    private $till;

    /**
     * @var string
     *
     * @ORM\Column(name="maxspeed", type="string", length=20, nullable=true)
     */
    private $maxspeed;

    /**
     * @var string
     *
     * @ORM\Column(name="s0to100", type="string", length=20, nullable=true)
     */
    private $s0to100;

    /**
     * @var int
     *
     * @ORM\Column(name="tank", type="smallint", nullable=true)
     */
    private $tank;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=100, nullable=true)
     */
    private $link;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
