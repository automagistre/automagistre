<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Embeddable\DatabaseConnectionOptions;
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

    /**
     * @var DatabaseConnectionOptions
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\DatabaseConnectionOptions")
     */
    public $database;

    public function __construct(string $name, DatabaseConnectionOptions $database)
    {
        $this->name = $name;
        $this->database = $database;
    }
}
