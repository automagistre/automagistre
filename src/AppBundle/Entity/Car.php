<?php

namespace AppBundle\Entity;

use AppBundle\Doctrine\PropertyAccessorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Car
 *
 * @ORM\Table(name="car", uniqueConstraints={@ORM\UniqueConstraint(name="UQ_3a7293440d99b39c56ff99074677931de71144cb", columns={"gosnomer"}), @ORM\UniqueConstraint(name="UQ_VIN", columns={"vin"})}, indexes={@ORM\Index(name="EID_IDX", columns={"eid"}), @ORM\Index(name="IDX_CAR_CLIENT", columns={"client_id"}), @ORM\Index(name="IDX_GOSNOMER", columns={"gosnomer"}), @ORM\Index(name="sprite_id", columns={"sprite_id"})})
 * @ORM\Entity
 */
class Car
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
     * @var Item
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Item")
     * @ORM\JoinColumn()
     */
    private $item;

    /**
     * @var Carmake
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Carmake")
     * @ORM\JoinColumn()
     */
    private $carmake;

    /**
     * @var Carmodel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Carmodel")
     * @ORM\JoinColumn()
     */
    private $carmodel;

    /**
     * @var Carmodification
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Carmodification")
     * @ORM\JoinColumn()
     */
    private $carmodification;

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
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumn()
     */
    private $client;

    /**
     * @var Mileage
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Mileage")
     * @ORM\JoinColumn()
     */
    private $mileage;

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

    public function displayName(): string
    {
        return sprintf('%s %s', $this->carmake->getName(), $this->carmodel->getName());
    }
}

