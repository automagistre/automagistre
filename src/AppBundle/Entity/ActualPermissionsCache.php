<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActualPermissionsCache.
 *
 * @ORM\Table(name="actual_permissions_cache")
 * @ORM\Entity
 */
class ActualPermissionsCache
{
    /**
     * @var int
     *
     * @ORM\Column(name="securableitem_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $securableitemId;

    /**
     * @var int
     *
     * @ORM\Column(name="permitable_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $permitableId;

    /**
     * @var bool
     *
     * @ORM\Column(name="allow_permissions", type="boolean", nullable=false)
     */
    private $allowPermissions;

    /**
     * @var bool
     *
     * @ORM\Column(name="deny_permissions", type="boolean", nullable=false)
     */
    private $denyPermissions;
}
