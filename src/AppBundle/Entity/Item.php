<?php

namespace AppBundle\Entity;

use AppBundle\Doctrine\PropertyAccessorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Item
 *
 * @ORM\Table(name="item")
 * @ORM\Entity
 */
class Item
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
     * @var \DateTime
     *
     * @ORM\Column(name="createddatetime", type="datetime", nullable=true)
     */
    private $createddatetime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifieddatetime", type="datetime", nullable=true)
     */
    private $modifieddatetime;

    /**
     * @var integer
     *
     * @ORM\Column(name="modifiedbyuser__user_id", type="integer", nullable=true)
     */
    private $modifiedbyuserUserId;

    /**
     * @var integer
     *
     * @ORM\Column(name="createdbyuser__user_id", type="integer", nullable=true)
     */
    private $createdbyuserUserId;

    /**
     * @var integer
     *
     * @ORM\Column(name="_user_id", type="integer", nullable=true)
     */
    private $userId;

}

