<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OrderReport
{
    use Identity;

    /**
     * @var string
     *
     * @ORM\Column(name="path", nullable=true)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", nullable=true)
     */
    private $filename;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenant\Order")
     * @ORM\JoinColumn
     */
    private $order;
}
