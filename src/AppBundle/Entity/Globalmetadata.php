<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Globalmetadata
 *
 * @ORM\Table(name="globalmetadata", uniqueConstraints={@ORM\UniqueConstraint(name="UQ_6950932d5c0020179c0a175933c8d60ccab633ae", columns={"classname"})})
 * @ORM\Entity
 */
class Globalmetadata
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
     * @ORM\Column(name="classname", type="string", length=64, nullable=true)
     */
    private $classname;

    /**
     * @var string
     *
     * @ORM\Column(name="serializedmetadata", type="text", length=65535, nullable=true)
     */
    private $serializedmetadata;

}

