<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class DatabaseConnectionOptions
{
    /**
     * @var string
     *
     * @ORM\Column
     */
    public $host;

    /**
     * @var string
     *
     * @ORM\Column
     */
    public $name;

    public function __construct(string $host, string $name)
    {
        $this->host = $host;
        $this->name = $name;
    }
}
