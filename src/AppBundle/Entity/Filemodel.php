<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Filemodel
 *
 * @ORM\Table(name="filemodel")
 * @ORM\Entity
 */
class Filemodel
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
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=128, nullable=true)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    private $size;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_id", type="integer", nullable=true)
     */
    private $itemId;

    /**
     * @var integer
     *
     * @ORM\Column(name="filecontent_id", type="integer", nullable=true)
     */
    private $filecontentId;

    /**
     * @var integer
     *
     * @ORM\Column(name="relatedmodel_id", type="integer", nullable=true)
     */
    private $relatedmodelId;

    /**
     * @var string
     *
     * @ORM\Column(name="relatedmodel_type", type="string", length=255, nullable=true)
     */
    private $relatedmodelType;

}

