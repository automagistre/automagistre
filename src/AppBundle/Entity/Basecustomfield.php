<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Basecustomfield
 *
 * @ORM\Table(name="basecustomfield")
 * @ORM\Entity
 */
class Basecustomfield
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
     * @var array
     *
     * @ORM\Column(name="data_customfielddata_id", type="simple_array", nullable=true)
     */
    private $dataCustomfielddataId;

}

