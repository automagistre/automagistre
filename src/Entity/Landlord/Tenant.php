<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Tenant
{
    use Identity;

    /**
     * @var string
     *
     * @ORM\Column
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(unique=true)
     */
    public $identifier;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
