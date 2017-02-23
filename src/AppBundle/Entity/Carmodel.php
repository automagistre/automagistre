<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Carmodel.
 *
 * @ORM\Table(name="carmodel", indexes={@ORM\Index(name="FK_am_models_am_makes", columns={"carmake_id"}), @ORM\Index(name="IDX_MODEL_FOLDER", columns={"folder"})})
 * @ORM\Entity
 */
class Carmodel
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
     * @var Carmake
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Carmake")
     * @ORM\JoinColumn()
     */
    private $carmake;

    /**
     * @var int
     *
     * @ORM\Column(name="folder", type="integer", nullable=true)
     */
    private $folder;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="name", type="string", length=30, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=100, nullable=true)
     */
    private $link;

    /**
     * @var bool
     *
     * @ORM\Column(name="loaded", type="boolean", nullable=true)
     */
    private $loaded = '0';

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return Carmake
     */
    public function getCarmake(): ?Carmake
    {
        return $this->carmake;
    }

    /**
     * @param Carmake $carmake
     */
    public function setCarmake(Carmake $carmake)
    {
        $this->carmake = $carmake;
    }
}
