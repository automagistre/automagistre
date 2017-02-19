<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Years
 *
 * @ORM\Table(name="_years")
 * @ORM\Entity
 */
class Years
{
    /**
     * @var integer
     *
     * @ORM\Column(name="val", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $val;

}

