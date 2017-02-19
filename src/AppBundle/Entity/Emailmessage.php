<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emailmessage
 *
 * @ORM\Table(name="emailmessage")
 * @ORM\Entity
 */
class Emailmessage
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
     * @ORM\Column(name="ownedsecurableitem_id", type="integer", nullable=true)
     */
    private $ownedsecurableitemId;

    /**
     * @var integer
     *
     * @ORM\Column(name="content_emailmessagecontent_id", type="integer", nullable=true)
     */
    private $contentEmailmessagecontentId;

    /**
     * @var integer
     *
     * @ORM\Column(name="sender_emailmessagesender_id", type="integer", nullable=true)
     */
    private $senderEmailmessagesenderId;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="text", length=65535, nullable=true)
     */
    private $subject;

    /**
     * @var integer
     *
     * @ORM\Column(name="folder_emailfolder_id", type="integer", nullable=true)
     */
    private $folderEmailfolderId;

    /**
     * @var integer
     *
     * @ORM\Column(name="error_emailmessagesenderror_id", type="integer", nullable=true)
     */
    private $errorEmailmessagesenderrorId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sentdatetime", type="datetime", nullable=true)
     */
    private $sentdatetime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sendondatetime", type="datetime", nullable=true)
     */
    private $sendondatetime;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sendattempts", type="boolean", nullable=true)
     */
    private $sendattempts;

}

