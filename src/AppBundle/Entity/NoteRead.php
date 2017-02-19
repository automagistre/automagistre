<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NoteRead
 *
 * @ORM\Table(name="note_read")
 * @ORM\Entity
 */
class NoteRead
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
     * @ORM\Column(name="munge_id", type="integer", nullable=true)
     */
    private $mungeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="securableitem_id", type="integer", nullable=true)
     */
    private $securableitemId;

}

