<?php

namespace AppBundle\Entity;

use AppBundle\Doctrine\PropertyAccessorTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Carmake.
 *
 * @ORM\Table(name="carmake", uniqueConstraints={@ORM\UniqueConstraint(name="unique_make", columns={"name"})}, indexes={@ORM\Index(name="IDX_MAKES_FOLDER", columns={"folder"})})
 * @ORM\Entity
 */
class Carmake
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

    public function __toString(): string
    {
        return (string) $this->getName();
    }
}
