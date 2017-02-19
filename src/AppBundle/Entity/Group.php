<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Group.
 *
 * @ORM\Table(name="_group", uniqueConstraints={@ORM\UniqueConstraint(name="UQ_f97279e76d95d98f7433ff400fab94e189ee6052", columns={"name"})})
 * @ORM\Entity
 */
class Group
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
     * @ORM\Column(name="permitable_id", type="integer", nullable=true)
     */
    private $permitableId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="_group_id", type="boolean", nullable=true)
     */
    private $groupId;
}
