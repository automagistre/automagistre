<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NoteRead.
 *
 * @ORM\Table(name="note_read")
 * @ORM\Entity
 */
class NoteRead
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
     * @ORM\Column(name="munge_id", type="integer", nullable=true)
     */
    private $mungeId;

    /**
     * @var int
     *
     * @ORM\Column(name="securableitem_id", type="integer", nullable=true)
     */
    private $securableitemId;
}
