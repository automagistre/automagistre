<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emailsignature.
 *
 * @ORM\Table(name="emailsignature")
 * @ORM\Entity
 */
class Emailsignature
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
     * @ORM\Column(name="_user_id", type="integer", nullable=true)
     */
    private $userId;

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
