<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityItem
 *
 * @ORM\Table(name="activity_item", uniqueConstraints={@ORM\UniqueConstraint(name="UQ_3072cf7f6632136338312839309d6fb046214edc", columns={"activity_id", "item_id"})})
 * @ORM\Entity
 */
class ActivityItem
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
     * @ORM\Column(name="activity_id", type="integer", nullable=true)
     */
    private $activityId;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_id", type="integer", nullable=true)
     */
    private $itemId;

}

