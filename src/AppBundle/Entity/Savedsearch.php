<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Savedsearch
 *
 * @ORM\Table(name="savedsearch")
 * @ORM\Entity
 */
class Savedsearch
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
     * @ORM\Column(name="ownedsecurableitem_id", type="integer", nullable=true)
     */
    private $ownedsecurableitemId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="serializeddata", type="text", length=65535, nullable=true)
     */
    private $serializeddata;

    /**
     * @var string
     *
     * @ORM\Column(name="viewclassname", type="string", length=64, nullable=true)
     */
    private $viewclassname;

}

