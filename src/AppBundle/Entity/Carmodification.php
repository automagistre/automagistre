<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="carmodification", indexes={@ORM\Index(name="IDX_MODIF_FOLDER", columns={"folder"}), @ORM\Index(name="IDX_MODIF_PARENT", columns={"cargeneration_id"})})
 * @ORM\Entity
 */
class Carmodification
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
     * @var Cargeneration
     *
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cargeneration")
     */
    private $cargeneration;

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
     * @Assert\Type("int")
     *
     * @ORM\Column(name="hp", type="smallint", nullable=true)
     */
    private $hp;

    /**
     * @var int
     *
     * @ORM\Column(name="doors", type="integer", nullable=true)
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
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return Cargeneration
     */
    public function getCargeneration(): ?Cargeneration
    {
        return $this->cargeneration;
    }

    /**
     * @param Cargeneration $cargeneration
     */
    public function setCargeneration(Cargeneration $cargeneration)
    {
        $this->cargeneration = $cargeneration;
    }

    /**
     * @return int
     */
    public function getHp(): ?int
    {
        return $this->hp;
    }

    /**
     * @param int $hp
     */
    public function setHp(int $hp)
    {
        $this->hp = $hp;
    }

    /**
     * @return int
     */
    public function getDoors(): ?int
    {
        return $this->doors;
    }

    /**
     * @param int $doors
     */
    public function setDoors(int $doors)
    {
        $this->doors = $doors;
    }

    /**
     * @return int
     */
    public function getFrom(): ?int
    {
        return $this->from;
    }

    /**
     * @param int $from
     */
    public function setFrom(int $from)
    {
        $this->from = $from;
    }

    /**
     * @return int
     */
    public function getTill(): ?int
    {
        return $this->till;
    }

    /**
     * @param int $till
     */
    public function setTill(int $till)
    {
        $this->till = $till;
    }

    /**
     * @return string
     */
    public function getMaxspeed(): ?string
    {
        return $this->maxspeed;
    }

    /**
     * @param string $maxspeed
     */
    public function setMaxspeed(string $maxspeed)
    {
        $this->maxspeed = $maxspeed;
    }

    /**
     * @return string
     */
    public function getS0to100(): ?string
    {
        return $this->s0to100;
    }

    /**
     * @param string $s0to100
     */
    public function setS0to100(string $s0to100)
    {
        $this->s0to100 = $s0to100;
    }

    /**
     * @return int
     */
    public function getTank(): ?int
    {
        return $this->tank;
    }

    /**
     * @param int $tank
     */
    public function setTank(int $tank)
    {
        $this->tank = $tank;
    }
}
