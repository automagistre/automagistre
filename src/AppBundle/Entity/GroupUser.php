<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GroupUser.
 *
 * @ORM\Table(name="_group__user", uniqueConstraints={@ORM\UniqueConstraint(name="UQ_8b7b9c47c851f14d46de32b2c5dd3ffd490b9319", columns={"_group_id", "_user_id"})})
 * @ORM\Entity
 */
class GroupUser
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
     * @ORM\Column(name="_group_id", type="integer", nullable=true)
     */
    private $groupId;

    /**
     * @var int
     *
     * @ORM\Column(name="_user_id", type="integer", nullable=true)
     */
    private $userId;
}
