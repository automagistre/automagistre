<?php

namespace AppBundle\Entity;

use AppBundle\Doctrine\PropertyAccessorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Carmodel.
 *
 * @ORM\Table(name="carmodel", indexes={@ORM\Index(name="FK_am_models_am_makes", columns={"carmake_id"}), @ORM\Index(name="IDX_MODEL_FOLDER", columns={"folder"})})
 * @ORM\Entity
 */
class Carmodel
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
     * @ORM\Column(name="carmake_id", type="integer", nullable=true)
     */
    private $carmakeId;

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
