<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ownedsecurableitem
 *
 * @ORM\Table(name="ownedsecurableitem")
 * @ORM\Entity
 */
class Ownedsecurableitem
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
     * @ORM\Column(name="securableitem_id", type="integer", nullable=true)
     */
    private $securableitemId;

    /**
     * @var integer
     *
     * @ORM\Column(name="owner__user_id", type="integer", nullable=true)
     */
    private $ownerUserId;

}

