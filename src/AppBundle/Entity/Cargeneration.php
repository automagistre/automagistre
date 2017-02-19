<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cargeneration
 *
 * @ORM\Table(name="cargeneration", indexes={@ORM\Index(name="IDX_GENERAT_PARENT", columns={"carmodel_id"}), @ORM\Index(name="IDX_GENERAT_FOLDER", columns={"folder"})})
 * @ORM\Entity
 */
class Cargeneration
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
     * @ORM\Column(name="carmodel_id", type="integer", nullable=true)
     */
    private $carmodelId;

    /**
     * @var integer
     *
     * @ORM\Column(name="folder", type="integer", nullable=true)
     */
    private $folder;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

}

