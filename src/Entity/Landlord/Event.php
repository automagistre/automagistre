<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedBy;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Enum\Tenant;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Event
{
    use Identity;
    use CreatedAt;
    use CreatedBy;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    private $arguments;

    /**
     * @var Tenant|null
     *
     * @ORM\Column(type="tenant_enum", nullable=true)
     */
    private $tenant;

    public function __construct(string $name, array $arguments, User $user, Tenant $tenant = null)
    {
        $this->name = $name;
        $this->arguments = $arguments;
        $this->tenant = $tenant;
        $this->setCreatedBy($user);
    }
}
