<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emailaccount.
 *
 * @ORM\Table(name="emailaccount")
 * @ORM\Entity
 */
class Emailaccount
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
     * @ORM\Column(name="item_id", type="integer", nullable=true)
     */
    private $itemId;

    /**
     * @var int
     *
     * @ORM\Column(name="_user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", length=65535, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="fromname", type="string", length=255, nullable=true)
     */
    private $fromname;

    /**
     * @var string
     *
     * @ORM\Column(name="replytoname", type="string", length=255, nullable=true)
     */
    private $replytoname;

    /**
     * @var string
     *
     * @ORM\Column(name="outboundhost", type="string", length=255, nullable=true)
     */
    private $outboundhost;

    /**
     * @var string
     *
     * @ORM\Column(name="outboundusername", type="string", length=255, nullable=true)
     */
    private $outboundusername;

    /**
     * @var string
     *
     * @ORM\Column(name="outboundpassword", type="string", length=255, nullable=true)
     */
    private $outboundpassword;

    /**
     * @var string
     *
     * @ORM\Column(name="outboundsecurity", type="string", length=255, nullable=true)
     */
    private $outboundsecurity;

    /**
     * @var string
     *
     * @ORM\Column(name="outboundtype", type="string", length=255, nullable=true)
     */
    private $outboundtype;

    /**
     * @var string
     *
     * @ORM\Column(name="fromaddress", type="string", length=255, nullable=true)
     */
    private $fromaddress;

    /**
     * @var bool
     *
     * @ORM\Column(name="usecustomoutboundsettings", type="boolean", nullable=true)
     */
    private $usecustomoutboundsettings;

    /**
     * @var bool
     *
     * @ORM\Column(name="outboundport", type="boolean", nullable=true)
     */
    private $outboundport;

    /**
     * @var array
     *
     * @ORM\Column(name="replytoaddress", type="simple_array", nullable=true)
     */
    private $replytoaddress;
}
