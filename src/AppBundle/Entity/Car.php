<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Car
 *
 * @ORM\Table(name="car", uniqueConstraints={@ORM\UniqueConstraint(name="UQ_3a7293440d99b39c56ff99074677931de71144cb", columns={"gosnomer"}), @ORM\UniqueConstraint(name="UQ_VIN", columns={"vin"})}, indexes={@ORM\Index(name="EID_IDX", columns={"eid"}), @ORM\Index(name="IDX_CAR_CLIENT", columns={"client_id"}), @ORM\Index(name="IDX_GOSNOMER", columns={"gosnomer"}), @ORM\Index(name="sprite_id", columns={"sprite_id"})})
 * @ORM\Entity
 */
class Car
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
     * @ORM\Column(name="item_id", type="integer", nullable=true)
     */
    private $itemId;

    /**
     * @var integer
     *
     * @ORM\Column(name="carmake_id", type="integer", nullable=true)
     */
    private $carmakeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="carmodel_id", type="integer", nullable=true)
     */
    private $carmodelId;

    /**
     * @var integer
     *
     * @ORM\Column(name="carmodification_id", type="integer", nullable=true)
     */
    private $carmodificationId;

    /**
     * @var string
     *
     * @ORM\Column(name="vin", type="string", length=17, nullable=true)
     */
    private $vin;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    private $year;

    /**
     * @var integer
     *
     * @ORM\Column(name="client_id", type="integer", nullable=true)
     */
    private $clientId;

    /**
     * @var integer
     *
     * @ORM\Column(name="mileage_id", type="integer", nullable=true)
     */
    private $mileageId;

    /**
     * @var string
     *
     * @ORM\Column(name="gosnomer", type="string", length=255, nullable=true)
     */
    private $gosnomer;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="eid", type="integer", nullable=true)
     */
    private $eid;

    /**
     * @var integer
     *
     * @ORM\Column(name="cargeneration_id", type="integer", nullable=true)
     */
    private $cargenerationId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="make_carmake_id", type="boolean", nullable=true)
     */
    private $makeCarmakeId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="model_carmodel_id", type="boolean", nullable=true)
     */
    private $modelCarmodelId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="modification_carmodification_id", type="boolean", nullable=true)
     */
    private $modificationCarmodificationId;

    /**
     * @var integer
     *
     * @ORM\Column(name="sprite_id", type="integer", nullable=true)
     */
    private $spriteId;

}

