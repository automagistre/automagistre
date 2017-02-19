<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Perusermetadata
 *
 * @ORM\Table(name="perusermetadata")
 * @ORM\Entity
 */
class Perusermetadata
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
     * @var integer
     *
     * @ORM\Column(name="_user_id", type="integer", nullable=true)
     */
    private $userId;

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

