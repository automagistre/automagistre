<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ownedcustomfield.
 *
 * @ORM\Table(name="ownedcustomfield")
 * @ORM\Entity
 */
class Ownedcustomfield
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
     * @ORM\Column(name="customfield_id", type="integer", nullable=true)
     */
    private $customfieldId;
}
