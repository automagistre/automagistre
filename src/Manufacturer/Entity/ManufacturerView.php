<?php

declare(strict_types=1);

namespace App\Manufacturer\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class ManufacturerView
{
    /**
     * @ORM\Column(type="manufacturer_id")
     */
    public ManufacturerId $id;

    /**
     * @ORM\Column(name="name")
     */
    public string $name;

    public function __construct(ManufacturerId $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
