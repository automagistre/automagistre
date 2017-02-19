<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notificationmessage
 *
 * @ORM\Table(name="notificationmessage")
 * @ORM\Entity
 */
class Notificationmessage
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
     * @var string
     *
     * @ORM\Column(name="htmlcontent", type="text", length=65535, nullable=true)
     */
    private $htmlcontent;

    /**
     * @var string
     *
     * @ORM\Column(name="textcontent", type="text", length=65535, nullable=true)
     */
    private $textcontent;

}

