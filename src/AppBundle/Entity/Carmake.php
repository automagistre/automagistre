<?php

namespace AppBundle\Entity;

use AppBundle\Doctrine\PropertyAccessorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Carmake
 *
 * @ORM\Table(name="carmake", uniqueConstraints={@ORM\UniqueConstraint(name="unique_make", columns={"name"})}, indexes={@ORM\Index(name="IDX_MAKES_FOLDER", columns={"folder"})})
 * @ORM\Entity
 */
class Carmake
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
     * @var boolean
     *
     * @ORM\Column(name="loaded", type="boolean", nullable=true)
     */
    private $loaded = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="choose", type="string", nullable=true)
     */
    private $choose = 'true';

    /**
     * @var string
     *
     * @ORM\Column(name="isParent", type="string", nullable=true)
     */
    private $isparent = 'true';

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}

