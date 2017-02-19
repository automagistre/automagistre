<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientRead.
 *
 * @ORM\Table(name="client_read", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */
class ClientRead
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
     * @ORM\Column(name="securableitem_id", type="integer", nullable=false)
     */
    private $securableitemId;

    /**
     * @var int
     *
     * @ORM\Column(name="munge_id", type="integer", nullable=false)
     */
    private $mungeId;

    /**
     * @var int
     *
     * @ORM\Column(name="count", type="integer", nullable=false)
     */
    private $count;
}
