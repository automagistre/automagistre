<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Activelanguage
 *
 * @ORM\Table(name="activelanguage")
 * @ORM\Entity
 */
class Activelanguage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=16, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="nativename", type="string", length=64, nullable=true)
     */
    private $nativename;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="activationdatetime", type="datetime", nullable=true)
     */
    private $activationdatetime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastupdatedatetime", type="datetime", nullable=true)
     */
    private $lastupdatedatetime;

}

