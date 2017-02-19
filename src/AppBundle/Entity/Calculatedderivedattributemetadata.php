<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Calculatedderivedattributemetadata.
 *
 * @ORM\Table(name="calculatedderivedattributemetadata")
 * @ORM\Entity
 */
class Calculatedderivedattributemetadata
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
     * @var int
     *
     * @ORM\Column(name="derivedattributemetadata_id", type="integer", nullable=true)
     */
    private $derivedattributemetadataId;
}
