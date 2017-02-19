<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emailmessage.
 *
 * @ORM\Table(name="emailmessage")
 * @ORM\Entity
 */
class Emailmessage
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
     * @ORM\Column(name="ownedsecurableitem_id", type="integer", nullable=true)
     */
    private $ownedsecurableitemId;

    /**
     * @var int
     *
     * @ORM\Column(name="content_emailmessagecontent_id", type="integer", nullable=true)
     */
    private $contentEmailmessagecontentId;

    /**
     * @var int
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
     * @var int
     *
     * @ORM\Column(name="folder_emailfolder_id", type="integer", nullable=true)
     */
    private $folderEmailfolderId;

    /**
     * @var int
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
     * @var bool
     *
     * @ORM\Column(name="sendattempts", type="boolean", nullable=true)
     */
    private $sendattempts;
}
