<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emailmessagecontent
 *
 * @ORM\Table(name="emailmessagecontent")
 * @ORM\Entity
 */
class Emailmessagecontent
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

