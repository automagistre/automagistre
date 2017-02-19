<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Derivedattributemetadata.
 *
 * @ORM\Table(name="derivedattributemetadata")
 * @ORM\Entity
 */
class Derivedattributemetadata
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="modelclassname", type="string", length=255, nullable=true)
     */
    private $modelclassname;

    /**
     * @var string
     *
     * @ORM\Column(name="serializedmetadata", type="string", length=255, nullable=true)
     */
    private $serializedmetadata;
}
