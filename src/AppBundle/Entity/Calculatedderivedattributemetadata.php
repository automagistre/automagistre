<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Calculatedderivedattributemetadata
 *
 * @ORM\Table(name="calculatedderivedattributemetadata")
 * @ORM\Entity
 */
class Calculatedderivedattributemetadata
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
     * @ORM\Column(name="derivedattributemetadata_id", type="integer", nullable=true)
     */
    private $derivedattributemetadataId;

}

